<?php namespace TeenQuotes\Models\Observers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Mail;
use Quote;
use TeenQuotes\Mail\MailSwitcher;

class CommentObserver {
	
	/**
	 * Will be triggered when a model is created
	 * @param Comment $comment
	 */
	public function created($comment)
	{
		$quote = Quote::where('id', '=', $comment->quote_id)
			->with('user')
			->first();

		// Send an email to the author of the quote if he wants it
		if ($quote->user->wantsEmailComment()) {

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