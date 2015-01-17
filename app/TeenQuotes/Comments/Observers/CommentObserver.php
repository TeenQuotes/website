<?php namespace TeenQuotes\Comments\Observers;

use App, Lang;
use TeenQuotes\Quotes\Models\Quote;

class CommentObserver {

	/**
	 * @var TeenQuotes\Quotes\Repositories\QuoteRepository
	 */
	private $quoteRepo;

	/**
	 * @var TeenQuotes\Mail\UserMailer
	 */
	private $userMailer;

	function __construct()
	{
		$this->quoteRepo = App::make('TeenQuotes\Quotes\Repositories\QuoteRepository');
		$this->userMailer = App::make('TeenQuotes\Mail\UserMailer');
	}

	/**
	 * Will be triggered when a model is created
	 * @param TeenQuotes\Comments\Models\Comment $comment
	 */
	public function created($comment)
	{
		$quote = $this->quoteRepo->getByIdWithUser($comment->quote_id);

		// Send an email to the author of the quote if he wants it
		if ($this->needToWarnByEmail($quote, $comment))
			$this->sendEmailToQuoteAuthor($quote);
	}

	private function sendEmailToQuoteAuthor($quote)
	{
		$author = $quote->user;

		$subject = Lang::get('comments.commentAddedSubjectEmail', ['id' => $quote->id]);

		$this->userMailer->send('emails.comments.posted',
			$author,
			compact('quote'),
			$subject
		);
	}

	private function needToWarnByEmail($quote, $comment)
	{
		// Do not send an e-mail if the author of the comment
		// has written the quote
		return $quote->user->wantsEmailComment() AND $comment->user_id != $quote->user->id;
	}
}