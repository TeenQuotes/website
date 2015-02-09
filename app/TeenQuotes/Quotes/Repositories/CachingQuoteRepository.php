<?php namespace TeenQuotes\Quotes\Repositories;

use Cache, Config;
use TeenQuotes\Quotes\Models\Quote;
use TeenQuotes\Tags\Models\Tag;
use TeenQuotes\Users\Models\User;

class CachingQuoteRepository implements QuoteRepository {

	/**
	 * @var \TeenQuotes\Quotes\Repositories\QuoteRepository
	 */
	private $quotes;

	public function __construct(QuoteRepository $quotes)
	{
		$this->quotes = $quotes;
		// We shouldn't inject big dependencies, like Cache or Config
		// Because it would take a massive space when serializing
		// every classes when caching a result
	}

	/**
	 * @see \TeenQuotes\Quotes\Repositories\QuoteRepository
	 */
	public function lastWaitingQuotes()
	{
		return $this->quotes->lastWaitingQuotes();
	}

	/**
	 * @see \TeenQuotes\Quotes\Repositories\QuoteRepository
	 */
	public function nbPending()
	{
		return $this->quotes->nbPending();
	}

	/**
	 * @see \TeenQuotes\Quotes\Repositories\QuoteRepository
	 */
	public function lastPendingQuotes($nb)
	{
		return $this->quotes->lastPendingQuotes($nb);
	}

	/**
	 * @see \TeenQuotes\Quotes\Repositories\QuoteRepository
	 */
	public function getById($id)
	{
		return $this->quotes->getById($id);
	}

	/**
	 * @see \TeenQuotes\Quotes\Repositories\QuoteRepository
	 */
	public function getByIdWithUser($id)
	{
		return $this->quotes->getByIdWithUser($id);
	}

	/**
	 * @see \TeenQuotes\Quotes\Repositories\QuoteRepository
	 */
	public function waitingById($id)
	{
		return $this->quotes->waitingById($id);
	}

	/**
	 * @see \TeenQuotes\Quotes\Repositories\QuoteRepository
	 */
	public function showQuote($id)
	{
		return $this->quotes->showQuote($id);
	}

	/**
	 * @see \TeenQuotes\Quotes\Repositories\QuoteRepository
	 */
	public function countQuotesByApprovedForUser($approved, User $u)
	{
		return $this->quotes->countQuotesByApprovedForUser($approved, $u);
	}

	/**
	 * @see \TeenQuotes\Quotes\Repositories\QuoteRepository
	 */
	public function updateContentAndApproved($id, $content, $approved)
	{
		return $this->quotes->updateContentAndApproved($id, $content, $approved);
	}

	/**
	 * @see \TeenQuotes\Quotes\Repositories\QuoteRepository
	 */
	public function updateApproved($id, $approved)
	{
		if ($approved == Quote::PUBLISHED)
			$this->flushQuotesForQuote($id);

		return $this->quotes->updateApproved($id, $approved);
	}

	/**
	 * @see \TeenQuotes\Quotes\Repositories\QuoteRepository
	 */
	public function totalPublished()
	{
		$quotes = $this->quotes;

		return Cache::remember('quotes.totalPublished', 10, function() use($quotes)
		{
			return $quotes->totalPublished();
		});
	}

	/**
	 * @see \TeenQuotes\Quotes\Repositories\QuoteRepository
	 */
	public function searchCountPublishedWithQuery($query)
	{
		return $this->quotes->searchCountPublishedWithQuery($query);
	}

	/**
	 * @see \TeenQuotes\Quotes\Repositories\QuoteRepository
	 */
	public function submittedTodayForUser(User $u)
	{
		return $this->quotes->submittedTodayForUser($u);
	}

	/**
	 * @see \TeenQuotes\Quotes\Repositories\QuoteRepository
	 */
	public function createQuoteForUser(User $u, $content)
	{
		return $this->quotes->createQuoteForUser($u, $content);
	}

	/**
	 * @see \TeenQuotes\Quotes\Repositories\QuoteRepository
	 */
	public function index($page, $pagesize)
	{
		$quotes = $this->quotes;

		// Use caching only with the default pagesize
		if (! $this->isDefaultPagesize($pagesize))
			return $this->quotes->index($page, $pagesize);

		return Cache::remember('quotes.published.'.$page, 1, function() use ($quotes, $page, $pagesize)
		{
			return $quotes->index($page, $pagesize);
		});
	}

	/**
	 * @see \TeenQuotes\Quotes\Repositories\QuoteRepository
	 */
	public function randomPublished($nb)
	{
		return $this->quotes->randomPublished($nb);
	}

	/**
	 * @see \TeenQuotes\Quotes\Repositories\QuoteRepository
	 */
	public function randomPublishedToday($nb)
	{
		return $this->quotes->randomPublishedToday($nb);
	}

	/**
	 * @see \TeenQuotes\Quotes\Repositories\QuoteRepository
	 */
	public function indexRandom($page, $pagesize)
	{
		$quotes = $this->quotes;

		// Use caching only with the default pagesize
		if (! $this->isDefaultPagesize($pagesize))
			return $this->quotes->indexRandom($page, $pagesize);

		return Cache::remember('quotes.random.'.$page, 1, function() use ($quotes, $page, $pagesize)
		{
			return $quotes->indexRandom($page, $pagesize);
		});
	}

	/**
	 * @see \TeenQuotes\Quotes\Repositories\QuoteRepository
	 */
	public function listPublishedIdsForUser(User $u)
	{
		return $this->quotes->listPublishedIdsForUser($u);
	}

	/**
	 * @see \TeenQuotes\Quotes\Repositories\QuoteRepository
	 */
	public function nbPublishedForUser(User $u)
	{
		$quotes = $this->quotes;

		return Cache::remember('quotes.user-'.$u->id.'.nbPublished', 10, function() use($quotes, $u)
		{
			return $quotes->nbPublishedForUser($u);
		});
	}

	/**
	 * @see \TeenQuotes\Quotes\Repositories\QuoteRepository
	 */
	public function getForIds($ids, $page, $pagesize)
	{
		return $this->quotes->getForIds($ids, $page, $pagesize);
	}

	/**
	 * @see \TeenQuotes\Quotes\Repositories\QuoteRepository
	 */
	public function searchPublishedWithQuery($query, $page, $pagesize)
	{
		return $this->quotes->searchPublishedWithQuery($query, $page, $pagesize);
	}

	/**
	 * @see \TeenQuotes\Quotes\Repositories\QuoteRepository
	 */
	public function getQuotesByApprovedForUser(User $u, $approved, $page, $pagesize)
	{
		$quotes = $this->quotes;

		$callback = (function() use ($quotes, $u, $approved, $page, $pagesize)
		{
			return $quotes->getQuotesByApprovedForUser($u, $approved, $page, $pagesize);
		});

		$cacheTags = $this->getCacheNameForUserAndApproved($u, $approved);

		return Cache::tags($cacheTags)->remember($page, 5, $callback);
	}

	/**
	 * @see \TeenQuotes\Quotes\Repositories\QuoteRepository
	 */
	public function nbDaysUntilPublication($q)
	{
		return $this->quotes->nbDaysUntilPublication($q);
	}

	/**
	 * @see \TeenQuotes\Quotes\Repositories\QuoteRepository
	 */
	public function nbQuotesWithFavorites()
	{
		return $this->quotes->nbQuotesWithFavorites();
	}

	/**
	 * @see \TeenQuotes\Quotes\Repositories\QuoteRepository
	 */
	public function nbQuotesWithComments()
	{
		return $this->quotes->nbQuotesWithComments();
	}

	/**
	 * @see \TeenQuotes\Quotes\Repositories\QuoteRepository
	 */
	public function getQuotesForTag(Tag $t, $page, $pagesize)
	{
		return $this->quotes->getQuotesForTag($t, $page, $pagesize);
	}

	private function flushQuotesForQuote($id)
	{
		$quote = $this->getByIdWithUser($id);
		$author = $quote->user;

		// Increment the number of published quotes for the author
		if (Cache::has('quotes.user-'.$author->id.'.nbPublished'))
			Cache::increment('quotes.user-'.$author->id.'.nbPublished');

		// Update the number of published quotes
		if (Cache::has('quotes.totalPublished'))
			Cache::increment('quotes.totalPublished');

		// Delete published and waiting quotes for the author
		Cache::tags($this->getCacheNameForUserAndApproved($author, Quote::WAITING))->flush();
		Cache::tags($this->getCacheNameForUserAndApproved($author, Quote::PUBLISHED))->flush();
	}

	private function getCacheNameForUserAndApproved(User $u, $approve)
	{
		return ['quotes', 'user', $u->id, $approve];
	}

	/**
	 * Tells if the pagesize is the default value
	 *
	 * @param  int  $pagesize
	 * @return boolean
	 */
	private function isDefaultPagesize($pagesize)
	{
		return $pagesize == Config::get('app.quotes.nbQuotesPerPage');
	}
}