<?php

namespace TeenQuotes\Quotes\Repositories;

use Carbon;
use TeenQuotes\Tags\Models\Tag;
use TeenQuotes\Users\Models\User;

interface QuoteRepository
{
    /**
     * Retrieve last waiting quotes.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function lastWaitingQuotes();

    /**
     * Get the number of quotes waiting to be published.
     *
     * @return int
     */
    public function nbPending();

    /**
     * Get the number of quotes waiting moderation.
     *
     * @return int
     */
    public function nbWaiting();

    /**
     * Grab pending quotes.
     *
     * @param int $nb The number of quotes to grab
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function lastPendingQuotes($nb);

    /**
     * Retrieve a quote by its ID.
     *
     * @param int $id
     *
     * @return \TeenQuotes\Quotes\Models\Quote
     */
    public function getById($id);

    /**
     * Retrieve a quote by its ID.
     *
     * @param int $id
     *
     * @return \TeenQuotes\Quotes\Models\Quote
     */
    public function getByIdWithUser($id);

    /**
     * Retrieve a waiting quote by its ID.
     *
     * @param int $id
     *
     * @return \TeenQuotes\Quotes\Models\Quote
     */
    public function waitingById($id);

    /**
     * Get full information about a quote given its ID.
     *
     * @param int $id
     *
     * @return \TeenQuotes\Quotes\Models\Quote
     */
    public function showQuote($id);

    /**
     * Count the number of quotes by approved type for a user.
     *
     * @param string                        $approved
     * @param \TeenQuotes\Users\Models\User $u
     *
     * @return int
     */
    public function countQuotesByApprovedForUser($approved, User $u);

    /**
     * Update the content and the approved type for a quote.
     *
     * @param int    $id
     * @param string $content
     * @param int    $approved
     *
     * @return \TeenQuotes\Quotes\Models\Quote
     */
    public function updateContentAndApproved($id, $content, $approved);

    /**
     * Update the approved type for a quote.
     *
     * @param int $id
     * @param int $approved
     *
     * @return \TeenQuotes\Quotes\Models\Quote
     */
    public function updateApproved($id, $approved);

    /**
     * Get the number of published quotes.
     *
     * @return int
     */
    public function totalPublished();

    /**
     * Get the number of results for a query against published quotes.
     *
     * @param string $query
     *
     * @return int
     */
    public function searchCountPublishedWithQuery($query);

    /**
     * Get the number of quotes submitted today for a user.
     *
     * @param \TeenQuotes\Users\Models\User $u
     *
     * @return int
     */
    public function submittedTodayForUser(User $u);

    /**
     * Create a quote for a user.
     *
     * @param \TeenQuotes\Users\Models\User $u
     * @param string                        $content
     *
     * @return \TeenQuotes\Quotes\Models\Quote
     */
    public function createQuoteForUser(User $u, $content);

    /**
     * List published quotes for a given page and pagesize.
     *
     * @param int $page
     * @param int $pagesize
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function index($page, $pagesize);

    /**
     * Retrieve some random published quotes.
     *
     * @param int $nb
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function randomPublished($nb);

    /**
     * Retrieve some random published quotes, published today.
     *
     * @param int $nb
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function randomPublishedToday($nb);

    /**
     * List published random quotes for a given page and pagesize.
     *
     * @param int $page
     * @param int $pagesize
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function indexRandom($page, $pagesize);

    /**
     * List IDs of published quotes for a user.
     *
     * @param \TeenQuotes\Users\Models\User $u
     *
     * @return array
     */
    public function listPublishedIdsForUser(User $u);

    /**
     * Get the number of published quotes for a user.
     *
     * @param \TeenQuotes\Users\Models\User $u
     *
     * @return int
     */
    public function nbPublishedForUser(User $u);

    /**
     * Retrieve quotes for given IDs, page and pagesize.
     *
     * @param array $ids
     * @param int   $page
     * @param int   $pagesize
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getForIds($ids, $page, $pagesize);

    /**
     * Search published quote with a query.
     *
     * @param string $query
     * @param int    $page
     * @param int    $pagesize
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function searchPublishedWithQuery($query, $page, $pagesize);

    /**
     * Get quotes by approved type for a user.
     *
     * @param \TeenQuotes\Users\Models\User $u
     * @param string                        $approved
     * @param int                           $page
     * @param int                           $pagesize
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getQuotesByApprovedForUser(User $u, $approved, $page, $pagesize);

    /**
     * Compute the number of days before publication for a quote waiting to be published.
     *
     * @param \TeenQuotes\Quotes\Models\Quote|int $q
     *
     * @return int
     *
     * @throws \InvalidArgumentException If the quote is not waiting to be published
     */
    public function nbDaysUntilPublication($q);

    /**
     * Get the number of quotes having at least a favorite.
     *
     * @return int
     */
    public function nbQuotesWithFavorites();

    /**
     * Get the number of quotes having at least a comment.
     *
     * @return int
     */
    public function nbQuotesWithComments();

    /**
     * Get quotes having a given tag, for a page and a pagesize.
     *
     * @param \TeenQuotes\Tags\Models\Tag $t
     * @param int                         $page
     * @param int                         $pagesize
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getQuotesForTag(Tag $t, $page, $pagesize);

    /**
     * Get the number of waiting quotes submitted after a given date.
     *
     * @param \Carbon\Carbon $date
     *
     * @return int
     */
    public function countWaitingQuotesSince(Carbon $date);
}
