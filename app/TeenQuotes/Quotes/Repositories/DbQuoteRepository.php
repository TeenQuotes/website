<?php namespace TeenQuotes\Quotes\Repositories;

use Config, DB, InvalidArgumentException;
use Illuminate\Database\Eloquent\Collection;
use TeenQuotes\Quotes\Models\Quote;
use TeenQuotes\Tags\Models\Tag;
use TeenQuotes\Users\Models\User;

class DbQuoteRepository implements QuoteRepository {

	/**
	 * Retrieve last waiting quotes
	 * @return \Illuminate\Database\Eloquent\Collection
	 */
	public function lastWaitingQuotes()
	{
		return Quote::waiting()
			->orderAscending()
			->get();
	}

	/**
	 * Get the number of quotes waiting to be published
	 * @return int
	 */
	public function nbPending()
	{
		return Quote::pending()->count();
	}

	/**
	 * Grab pending quotes
	 * @param  int $nb The number of quotes to grab
	 * @return \Illuminate\Database\Eloquent\Collection
	 */
	public function lastPendingQuotes($nb)
	{
		return Quote::pending()
			->orderAscending()
			->take($nb)
			->with('user')
			->get();
	}

	/**
	 * Retrieve a quote by its ID
	 * @param  int $id
	 * @return \TeenQuotes\Quotes\Models\Quote
	 */
	public function getById($id)
	{
		return Quote::find($id);
	}

	/**
	 * Retrieve a quote by its ID
	 * @param  int $id
	 * @return \TeenQuotes\Quotes\Models\Quote
	 */
	public function getByIdWithUser($id)
	{
		return Quote::whereId($id)
			->with('user')
			->first();
	}

	/**
	 * Retrieve a waiting quote by its ID
	 * @param  int $id
	 * @return \TeenQuotes\Quotes\Models\Quote
	 */
	public function waitingById($id)
	{
		return Quote::waiting()
			->where('id', '=', $id)
			->with('user')
			->first();
	}

	/**
	 * Get full information about a quote given its ID
	 * @param  int $id
	 * @return \TeenQuotes\Quotes\Models\Quote
	 */
	public function showQuote($id)
	{
		return Quote::whereId($id)
			->with('comments')
			->withSmallUser('comments.user')
			->withSmallUser('favorites.user')
			->withSmallUser()
			->first();
	}

	/**
	 * Count the number of quotes by approved type for a user
	 * @param  string $approved
	 * @param  \TeenQuotes\Users\Models\User $u
	 * @return int
	 */
	public function countQuotesByApprovedForUser($approved, User $u)
	{
		$this->guardApprovedScope($approved);

		return Quote::$approved()
			->forUser($u)
			->count();
	}

	/**
	 * Update the content and the approved type for a quote
	 * @param  int $id
	 * @param  string $content
	 * @param  int $approved
	 * @return \TeenQuotes\Quotes\Models\Quote
	 */
	public function updateContentAndApproved($id, $content, $approved)
	{
		$this->guardApproved($approved);

		$q = $this->getById($id);
		$q->content = $content;
		$q->approved = $approved;
		$q->save();

		return $q;
	}

	/**
	 * Update the approved type for a quote
	 * @param  int $id
	 * @param  int $approved
	 * @return \TeenQuotes\Quotes\Models\Quote
	 */
	public function updateApproved($id, $approved)
	{
		$this->guardApproved($approved);

		$q = $this->getById($id);
		$q->approved = $approved;
		$q->save();

		return $q;
	}

	/**
	 * Get the number of published quotes
	 * @return int
	 */
	public function totalPublished()
	{
		return Quote::published()->count();
	}

	/**
	 * Get the number of results for a query against published quotes
	 * @param  string $query
	 * @return int
	 */
	public function searchCountPublishedWithQuery($query)
	{
		return Quote::whereRaw("MATCH(content) AGAINST(?)", array($query))
			// $query will NOT be bind here
			// it will be bind when calling setBindings
			->where('approved', '=', Quote::PUBLISHED)
			// WARNING 1 corresponds to approved = 1
			// We need to bind it again
			->setBindings([$query, Quote::PUBLISHED])
			->count();
	}

	/**
	 * Get the number of quotes submitted today for a user
	 * @param  \TeenQuotes\Users\Models\User $u
	 * @return int
	 */
	public function submittedTodayForUser(User $u)
	{
		return Quote::createdToday()
			->forUser($u)
			->count();
	}

	/**
	 * Create a quote for a user
	 * @param  \TeenQuotes\Users\Models\User $u
	 * @param  string $content
	 * @return \TeenQuotes\Quotes\Models\Quote
	 */
	public function createQuoteForUser(User $u, $content)
	{
		$quote = new Quote;
		$quote->content = $content;
		$u->quotes()->save($quote);

		return $quote;
	}

	/**
	 * List published quotes for a given page and pagesize
	 * @param  int $page
	 * @param  int $pagesize
	 * @return \Illuminate\Database\Eloquent\Collection
	 */
	public function index($page, $pagesize)
	{
		return Quote::published()
			->withSmallUser()
			->orderDescending()
			->take($pagesize)
			->skip($this->computeSkip($page, $pagesize))
			->get();
	}

	/**
	 * Retrieve some random published quotes
	 * @param  int $nb
	 * @return \Illuminate\Database\Eloquent\Collection
	 */
	public function randomPublished($nb)
	{
		return Quote::published()
			->with('user')
			->random()
			->take($nb)
			->get();
	}

	/**
	 * Retrieve some random published quotes, published today
	 * @param  int $nb
	 * @return \Illuminate\Database\Eloquent\Collection
	 */
	public function randomPublishedToday($nb)
	{
		return Quote::published()
			->updatedToday()
			->random()
			->with('user')
			->take($nb)
			->get();
	}

	/**
	 * List published random quotes for a given page and pagesize
	 * @param  int $page
	 * @param  int $pagesize
	 * @return \Illuminate\Database\Eloquent\Collection
	 */
	public function indexRandom($page, $pagesize)
	{
		return Quote::published()
			->withSmallUser()
			->random()
			->take($pagesize)
			->skip($this->computeSkip($page, $pagesize))
			->get();
	}

	/**
	 * List IDs of published quotes for a user
	 * @param  \TeenQuotes\Users\Models\User $u
	 * @return array
	 */
	public function listPublishedIdsForUser(User $u)
	{
		return Quote::forUser($u)
			->published()
			->lists('id');
	}

	/**
	 * Get the number of published quotes for a user
	 * @param  \TeenQuotes\Users\Models\User $u
	 * @return int
	 */
	public function nbPublishedForUser(User $u)
	{
		return Quote::forUser($u)
			->published()
			->count();
	}

	/**
	 * Retrieve quotes for given IDs, page and pagesize
	 * @param  array $ids
	 * @param  int $page
	 * @param  int $pagesize
	 * @return \Illuminate\Database\Eloquent\Collection
	 */
	public function getForIds($ids, $page, $pagesize)
	{
		if (empty($ids)) return new Collection;

		$query = Quote::whereIn('id', $ids)
			->withSmallUser()
			->take($pagesize)
			->skip($this->computeSkip($page, $pagesize));

		if (Config::get('database.default') == 'mysql')
			$query = $query->orderBy(DB::raw("FIELD(id, ".implode(',', $ids).")"));

		return $query->get();
	}

	/**
	 * Search published quote with a query
	 * @param  string $query
	 * @param  int $page
	 * @param  int $pagesize
	 * @return \Illuminate\Database\Eloquent\Collection
	 */
	public function searchPublishedWithQuery($query, $page, $pagesize)
	{
		return Quote::select('id', 'content', 'user_id', 'approved', 'created_at', 'updated_at', DB::raw("MATCH(content) AGAINST(?) AS `rank`"))
			// $search will NOT be bind here
			// it will be bind when calling setBindings
			->whereRaw("MATCH(content) AGAINST(?)", array($query))
			->where('approved', '=', 1)
			->orderBy('rank', 'DESC')
			->withSmallUser()
			->skip($this->computeSkip($page, $pagesize))
			->take($pagesize)
			// WARNING 1 corresponds to approved = 1
			// We need to bind it again
			->setBindings([$query, $query, 1])
			->get();
	}

	/**
	 * Get quotes by approved type for a user
	 * @param  \TeenQuotes\Users\Models\User $u
	 * @param  string $approved
	 * @param  int $page
	 * @param  int $pagesize
	 * @return \Illuminate\Database\Eloquent\Collection
	 */
	public function getQuotesByApprovedForUser(User $u, $approved, $page, $pagesize)
	{
		$this->guardApprovedScope($approved);

		return Quote::$approved()
			->withSmallUser()
			->forUser($u)
			->orderDescending()
			->take($pagesize)
			->skip($this->computeSkip($page, $pagesize))
			->get();
	}

	/**
	 * Compute the number of days before publication for a quote waiting to be published.
	 * @param  \TeenQuotes\Quotes\Models\Quote|int $q
	 * @return int
	 * @throws \InvalidArgumentException If the quote is not waiting to be published
	 */
	public function nbDaysUntilPublication($q)
	{
		$q = $this->getQuote($q);

		if (! $q->isPending())
			throw new InvalidArgumentException("Quote #".$q->id." is not waiting to be published.");

		return ceil($this->getNbQuotesToPublishBefore($q) / $this->getNbQuotesToPublishPerDay());
	}

	/**
	 * Get the number of quotes having at least a favorite
	 * @return int
	 */
	public function nbQuotesWithFavorites()
	{
		return Quote::has('favorites')->count();
	}

	/**
	 * Get quotes having a given tag, for a page and a pagesize
	 * @param  \TeenQuotes\Tags\Models\Tag $t
	 * @param  int $page
	 * @param  int $pagesize
	 * @return \Illuminate\Database\Eloquent\Collection
	 */
	public function getQuotesForTag(Tag $t, $page, $pagesize)
	{
		return $t->quotes()
			->withSmallUser()
			->orderDescending()
			->take($pagesize)
			->skip($this->computeSkip($page, $pagesize))
			->get();
	}

	/**
	 * Get the number of quotes having at least a comment
	 * @return int
	 */
	public function nbQuotesWithComments()
	{
		return Quote::has('comments')->count();
	}

	private function getNbQuotesToPublishBefore(Quote $q)
	{
		$pending = $this->lastPendingQuotes(1000);

		$id = $q->id;

		$nbToPublishedBefore = $pending->filter(function($quote) use($id)
		{
			return $quote->id <= $id;
		})->count();

		return $nbToPublishedBefore;
	}

	/**
	 * Get a quote object
	 * @param  \TeenQuotes\Quotes\Models\Quote|int $q
	 * @return \TeenQuotes\Quotes\Models\Quote
	 */
	private function getQuote($q)
	{
		if ($q instanceof Quote)
			return $q;

		return $this->getById($q);
	}

	private function getNbQuotesToPublishPerDay()
	{
		return Config::get('app.quotes.nbQuotesToPublishPerDay');
	}

	private function guardApprovedScope($approved)
	{
		if (! in_array($approved, ['pending', 'refused', 'waiting', 'published']))
			throw new InvalidArgumentException("Wrong approved type. Got ".$approved);
	}

	private function guardApproved($approved)
	{
		if (! in_array($approved, [Quote::PENDING, Quote::REFUSED, Quote::WAITING, Quote::PUBLISHED]))
			throw new InvalidArgumentException("Wrong approved type. Got ".$approved);
	}

	private function computeSkip($page, $pagesize)
	{
		return $pagesize * ($page - 1);
	}
}