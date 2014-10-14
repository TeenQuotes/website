<?php namespace TeenQuotes\Comments\Observers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Mail;
use TeenQuotes\Mail\MailSwitcher;
use TeenQuotes\Quotes\Models\Quote;

class CommentObserver {

	/**
	 * @var TeenQuotes\Quotes\Repositories\QuoteRepository
	 */
	private $quoteRepo;

	function __construct()
	{
		$this->quoteRepo = App::make('TeenQuotes\Quotes\Repositories\QuoteRepository');
	}

	/**
	 * Will be triggered when a model is created
	 * @param Comment $comment
	 */
	public function created($comment)
	{
		$quote = $this->quoteRepo->getByIdWithUser($comment->quote_id);

		// Send an email to the author of the quote if he wants it
		// Do not send an e-mail if the author of the comment has written
		// the quote
		if ($quote->user->wantsEmailComment() AND $comment->user_id != $quote->user->id) {

			// Send the email via SMTP
			new MailSwitcher('smtp');
			Mail::send('emails.comments.posted', compact('quote'), function($m) use($quote)
			{
				$m->to($quote->user->email, $quote->user->login)->subject(Lang::get('comments.commentAddedSubjectEmail', ['id' => $quote->id]));
			});
		}

		// If we have the number of comments in cache, increment it
		if (Cache::has(Quote::$cacheNameNbComments.$comment->quote_id))
			Cache::increment(Quote::$cacheNameNbComments.$comment->quote_id);
	}

	/**
	 * Will be triggered when a model is deleted
	 * @param Comment $comment
	 */
	public function deleted($comment)
	{
		// Update the number of comments on the related quote in cache
		if (Cache::has(Quote::$cacheNameNbComments.$comment->quote_id))
			Cache::decrement(Quote::$cacheNameNbComments.$comment->quote_id);
	}
}