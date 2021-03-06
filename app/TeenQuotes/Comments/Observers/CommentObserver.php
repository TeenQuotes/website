<?php

/*
 * This file is part of the Teen Quotes website.
 *
 * (c) Antoine Augusti <antoine.augusti@teen-quotes.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TeenQuotes\Comments\Observers;

use App;
use TeenQuotes\Quotes\Models\Quote;

class CommentObserver
{
    /**
     * @var \TeenQuotes\Quotes\Repositories\QuoteRepository
     */
    private $quoteRepo;

    /**
     * @var \TeenQuotes\Mail\UserMailer
     */
    private $userMailer;

    public function __construct()
    {
        $this->quoteRepo  = App::make('TeenQuotes\Quotes\Repositories\QuoteRepository');
        $this->userMailer = App::make('TeenQuotes\Mail\UserMailer');
    }

    /**
     * Will be triggered when a model is created.
     *
     * @param \TeenQuotes\Comments\Models\Comment $comment
     */
    public function created($comment)
    {
        $quote = $this->quoteRepo->getByIdWithUser($comment->quote_id);

        // Send an email to the author of the quote if he wants it
        if ($this->needToWarnByEmail($quote, $comment)) {
            $this->sendEmailToQuoteAuthor($quote);
        }
    }

    private function sendEmailToQuoteAuthor($quote)
    {
        $author = $quote->user;

        $this->userMailer->warnAuthorAboutCommentPosted($author, $quote);
    }

    private function needToWarnByEmail($quote, $comment)
    {
        // Do not send an e-mail if the author of the comment
        // is the author of the quote
        return $quote->user->wantsEmailComment() and $comment->user_id != $quote->user->id;
    }
}
