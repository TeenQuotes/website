<?php

/*
 * This file is part of the Teen Quotes website.
 *
 * (c) Antoine Augusti <antoine.augusti@teen-quotes.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TeenQuotes\Comments\Repositories;

use Cache;
use TeenQuotes\Quotes\Models\Quote;
use TeenQuotes\Users\Models\User;

class CachingCommentRepository implements CommentRepository
{
    /**
     * @see \TeenQuotes\Comments\Repositories\CommentRepository
     */
    private $comments;

    public function __construct(CommentRepository $comments)
    {
        $this->comments = $comments;
    }

    /**
     * @see \TeenQuotes\Comments\Repositories\CommentRepository
     */
    public function findById($id)
    {
        return $this->comments->findById($id);
    }

    /**
     * @see \TeenQuotes\Comments\Repositories\CommentRepository
     */
    public function findByIdWithQuote($id)
    {
        return $this->comments->findByIdWithQuote($id);
    }

    /**
     * @see \TeenQuotes\Comments\Repositories\CommentRepository
     */
    public function indexForQuote($quote_id, $page, $pagesize)
    {
        return $this->comments->indexForQuote($quote_id, $page, $pagesize);
    }

    /**
     * @see \TeenQuotes\Comments\Repositories\CommentRepository
     */
    public function indexForQuoteWithQuote($quote_id, $page, $pagesize)
    {
        return $this->comments->indexForQuoteWithQuote($quote_id, $page, $pagesize);
    }

    /**
     * @see \TeenQuotes\Comments\Repositories\CommentRepository
     */
    public function findForUser(User $user, $page, $pagesize)
    {
        return $this->comments->findForUser($user, $page, $pagesize);
    }

    /**
     * @see \TeenQuotes\Comments\Repositories\CommentRepository
     */
    public function countForUser(User $user)
    {
        return $this->comments->countForUser($user);
    }

    /**
     * @see \TeenQuotes\Comments\Repositories\CommentRepository
     */
    public function create(Quote $q, User $u, $content)
    {
        $cacheKey = $this->getCountKeyForQuote($q);

        if (Cache::has($cacheKey)) {
            Cache::increment($cacheKey);
        }

        return $this->comments->create($q, $u, $content);
    }

    /**
     * @see \TeenQuotes\Comments\Repositories\CommentRepository
     */
    public function update($c, $content)
    {
        return $this->comments->update($c, $content);
    }

    /**
     * @see \TeenQuotes\Comments\Repositories\CommentRepository
     */
    public function delete($id)
    {
        $comment = $this->findByIdWithQuote($id);

        $cacheKey = $this->getCountKeyForQuote($comment->quote);

        if (Cache::has($cacheKey)) {
            Cache::decrement($cacheKey);
        }

        return $this->comments->delete($id);
    }

    /**
     * @see \TeenQuotes\Comments\Repositories\CommentRepository
     */
    public function getTopQuotes($page, $pagesize)
    {
        return $this->comments->getTopQuotes($page, $pagesize);
    }

    /**
     * @see \TeenQuotes\Comments\Repositories\CommentRepository
     */
    public function nbCommentsForQuote(Quote $q)
    {
        $callback = (function () use ($q) {
            return $this->comments->nbCommentsForQuote($q);
        });

        return Cache::remember($this->getCountKeyForQuote($q), 10, $callback);
    }

    private function getCountKeyForQuote(Quote $q)
    {
        return 'comments.quote-'.$q->id.'.count';
    }
}
