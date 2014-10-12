<?php namespace TeenQuotes\Api\V1\Controllers;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use TeenQuotes\Api\V1\Interfaces\PaginatedContentInterface;
use TeenQuotes\Http\Facades\Response;
use TeenQuotes\Quotes\Models\Quote;
use User;

class QuotesController extends APIGlobalController implements PaginatedContentInterface {

	private $relationInvolved = 'quotes';

	public function show($quote_id)
	{
		$quote = Quote::whereId($quote_id)
			->with('comments')
			->withSmallUser('comments.user')
			->withSmallUser('favorites.user')
			->withSmallUser()
			->first();

		// Handle not found
		if (empty($quote) OR $quote->count() == 0)
			return Response::json([
				'status' => 'quote_not_found',
				'error'  => "The quote #".$quote_id." was not found",
			], 404);
					
		// Register the view in the recommendation engine
		$quote->registerViewAction();

		return Response::json($quote, 200, [], JSON_NUMERIC_CHECK);
	}

	public function indexFavoritesQuotes($user_id)
	{
		$page = $this->getPage();
		$this->relationInvolved = 'users';
		$pagesize = $this->getPagesize();

		$user = User::find($user_id);
		
		// Handle user not found
		if (is_null($user)) {
			$data = [
				'status' => 'user_not_found',
				'error'  => "The user #".$user_id." was not found",
			];

			return Response::json($data, 400);
		}

		// Get the list of favorite quotes
		$arrayIDFavoritesQuotesForUser = $user->arrayIDFavoritesQuotes();

		$totalQuotes = count($arrayIDFavoritesQuotesForUser);
		
		// Get quotes
		$content = array();
		if ($totalQuotes > 0)
			$content = $this->getQuotesFavorite($page, $pagesize, $user, $arrayIDFavoritesQuotesForUser);

		// Handle no quotes found
		if (is_null($content) OR empty($content) OR $content->count() == 0) {
			$data = [
				'status' => 404,
				'error' => 'No quotes have been found.'
			];

			return Response::json($data, 404);
		}

		$data = self::paginateContent($page, $pagesize, $totalQuotes, $content, 'quotes');
		
		return Response::json($data, 200, [], JSON_NUMERIC_CHECK);
	}

	public function indexByApprovedQuotes($quote_approved_type, $user_id)
	{
		$page = $this->getPage();
		$this->relationInvolved = 'users';
		$pagesize = $this->getPagesize();

		$user = User::find($user_id);
		
		// Handle user not found
		if (is_null($user)) {
			$data = [
				'status' => 'user_not_found',
				'error'  => "The user #".$user_id." was not found",
			];

			return Response::json($data, 400);
		}
		
		// Get quotes
		$content = $this->getQuotesByApprovedForUser($page, $pagesize, $user, $quote_approved_type);

		// Handle no quotes found
		$totalQuotes = 0;
		if (is_null($content) OR empty($content) OR $content->count() == 0) {
			$data = [
				'status' => 404,
				'error' => 'No quotes have been found.'
			];

			return Response::json($data, 404);
		}

		$totalQuotes = Quote::$quote_approved_type()->forUser($user)->count();

		$data = self::paginateContent($page, $pagesize, $totalQuotes, $content, 'quotes');
		
		return Response::json($data, 200, [], JSON_NUMERIC_CHECK);
	}

	public function index($random = null)
	{
		$page = $this->getPage();
		$pagesize = $this->getPagesize();

		$totalQuotes = Quote::nbQuotesPublished();

		// Get quotes
		if (is_null($random))
			$content = $this->getQuotesHome($page, $pagesize);
		else
			$content = $this->getQuotesRandom($page, $pagesize);

		// Handle no quotes found
		if (is_null($content) OR $content->count() == 0) {
			$data = [
				'status' => 404,
				'error' => 'No quotes have been found.'
			];

			return Response::json($data, 404);
		}

		$data = self::paginateContent($page, $pagesize, $totalQuotes, $content, 'quotes');
		
		return Response::json($data, 200, [], JSON_NUMERIC_CHECK);
	}

	public function random()
	{
		return $this->index(true);
	}

	public function getSearch($query)
	{
		$page = $this->getPage();
		$pagesize = $this->getPagesize();

		// Get quotes
		$content = self::getQuotesSearch($page, $pagesize, $query);

		// Handle no quotes found
		$totalQuotes = 0;
		if (is_null($content) OR empty($content) OR $content->count() == 0)
			return Response::json([
				'status' => 404,
				'error' => 'No quotes have been found.'
			], 404);

		$totalQuotes = Quote::whereRaw("MATCH(content) AGAINST(?)", array($query))
			// $query will NOT be bind here
			// it will be bind when calling setBindings
			->where('approved', '=', Quote::PUBLISHED)
			// WARNING 1 corresponds to approved = 1
			// We need to bind it again
			->setBindings([$query, Quote::PUBLISHED])
			->count();

		$data = self::paginateContent($page, $pagesize, $totalQuotes, $content, 'quotes');
		
		return Response::json($data, 200, [], JSON_NUMERIC_CHECK);
	}

	public function store($doValidation = true)
	{
		$user = $this->retrieveUser();
		$content = Input::get('content');

		if ($doValidation) {		
			
			// Validate content of the quote
			$validatorContent = Validator::make(compact('content'), ['content' => Quote::$rulesAdd['content']]);
			if ($validatorContent->fails()) {
				$data = [
					'status' => 'wrong_content',
					'error'  => 'Content of the quote should be between 50 and 300 characters.'
				];

				return Response::json($data, 400);
			}

			// Validate number of quotes submitted today
			$quotesSubmittedToday = Quote::createdToday()->forUser($user)->count();
			$validatorNbQuotes = Validator::make(compact('quotesSubmittedToday'), ['quotesSubmittedToday' => Quote::$rulesAdd['quotesSubmittedToday']]);
			if ($validatorNbQuotes->fails()) {
				$data = [
					'status' => 'too_much_submitted_quotes',
					'error'  => "The maximum number of quotes you can submit is 5 per day."
				];

				return Response::json($data, 400);
			}
		}

		// Store the quote
		$quote = new Quote;
		$quote->content = $content;
		$user->quotes()->save($quote);

		return Response::json($quote, 201, [], JSON_NUMERIC_CHECK);
	}

	public function getPagesize()
	{
		switch ($this->relationInvolved) {
			case 'users':
				return Input::get('pagesize', Config::get('app.users.nbQuotesPerPage'));

			case 'quotes':
				return Input::get('pagesize', $this->getDefaultNbQuotesPerPage());
		}
	}

	private function getDefaultNbQuotesPerPage()
	{
		return Config::get('app.quotes.nbQuotesPerPage');
	}

	private function getQuotesHome($page, $pagesize)
	{
		// Time to store in cache
		$expiresAt = Carbon::now()->addMinutes(1);

		// Number of quotes to skip
		$skip = $pagesize * ($page - 1);

		// We will hit the cache / remember in cache if we have the same pagesize
		// that the one of the website
		if ($pagesize == $this->getDefaultNbQuotesPerPage()) {
			$content = Cache::remember(Quote::$cacheNameQuotesAPIPage.$page, $expiresAt, function() use($pagesize, $skip) {
				return Quote::published()
					->withSmallUser()
					->orderDescending()
					->take($pagesize)
					->skip($skip)
					->get();
			});
		}
		else {
			$content = Quote::published()
				->withSmallUser()
				->orderDescending()
				->take($pagesize)
				->skip($skip)
				->get();
		}

		return $content;
	}

	private function getQuotesRandom($page, $pagesize)
	{
		// Time to store in cache
		$expiresAt = Carbon::now()->addMinutes(1);

		// Number of quotes to skip
		$skip = $pagesize * ($page - 1);

		// We will hit the cache / remember in cache if we have the same pagesize
		// that the one of the website
		if ($pagesize == $this->getDefaultNbQuotesPerPage()) {
			$content = Cache::remember(Quote::$cacheNameRandomAPIPage.$page, $expiresAt, function() use($pagesize, $skip) {
				return Quote::published()
					->withSmallUser()
					->random()
					->take($pagesize)
					->skip($skip)
					->get();
			});
		}
		else {
			$content = Quote::published()
				->withSmallUser()
				->random()
				->take($pagesize)
				->skip($skip)
				->get();
		}

		return $content;
	}

	private function getQuotesFavorite($page, $pagesize, $user, $arrayIDFavoritesQuotesForUser)
	{
		// Number of quotes to skip
		$skip = $pagesize * ($page - 1);

		$query = Quote::whereIn('id', $arrayIDFavoritesQuotesForUser)
			->withSmallUser()
			->take($pagesize)
			->skip($skip);

		if (Config::get('database.default') == 'mysql')
			$query = $query->orderBy(DB::raw("FIELD(id, ".implode(',', $arrayIDFavoritesQuotesForUser).")"));
		
		$content = $query->get();

		return $content;
	}

	public static function getQuotesSearch($page, $pagesize, $query)
	{
		// Number of quotes to skip
		$skip = $pagesize * ($page - 1);

		$quotes = Quote::select('id', 'content', 'user_id', 'approved', 'created_at', 'updated_at', DB::raw("MATCH(content) AGAINST(?) AS `rank`"))
			// $search will NOT be bind here
			// it will be bind when calling setBindings
			->whereRaw("MATCH(content) AGAINST(?)", array($query))
			->where('approved', '=', 1)
			->orderBy('rank', 'DESC')
			->withSmallUser()
			->skip($skip)
			->take($pagesize)
			// WARNING 1 corresponds to approved = 1
			// We need to bind it again
			->setBindings([$query, $query, 1])
			->get();

		return $quotes;
	}

	private function getQuotesByApprovedForUser($page, $pagesize, $user, $quote_approved_type)
	{
		// Number of quotes to skip
		$skip = $pagesize * ($page - 1);

		$content = Quote::$quote_approved_type()
			->withSmallUser()
			->forUser($user)
			->orderDescending()
			->take($pagesize)
			->skip($skip)
			->get();

		return $content;
	}
}