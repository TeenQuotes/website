<?php namespace TeenQuotes\Api\V1\Controllers;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use TeenQuotes\Api\V1\Interfaces\PaginatedContentInterface;
use TeenQuotes\Exceptions\ApiNotFoundException;
use TeenQuotes\Http\Facades\Response;
use TeenQuotes\Quotes\Models\Quote;

class QuotesController extends APIGlobalController implements PaginatedContentInterface {

	private $relationInvolved = 'quotes';

	public function show($quote_id)
	{
		$quote = $this->quoteRepo->showQuote($quote_id);

		// Handle not found
		if (empty($quote) OR $quote->count() == 0)
			return Response::json([
				'status' => 'quote_not_found',
				'error'  => "The quote #".$quote_id." was not found.",
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

		$user = $this->userRepo->getById($user_id);
		
		// Handle user not found
		if (is_null($user)) {
			$data = [
				'status' => 'user_not_found',
				'error'  => "The user #".$user_id." was not found.",
			];

			return Response::json($data, 400);
		}

		// Get the list of favorite quotes
		$arrayIDFavoritesQuotesForUser = $user->arrayIDFavoritesQuotes();

		$totalQuotes = count($arrayIDFavoritesQuotesForUser);
		
		// Get quotes
		$content = array();
		if ($totalQuotes > 0)
			$content = $this->getQuotesFavorite($page, $pagesize, $arrayIDFavoritesQuotesForUser);

		// Handle no quotes found
		if (is_null($content) OR empty($content) OR $content->count() == 0)
			throw new ApiNotFoundException('quotes');

		$data = self::paginateContent($page, $pagesize, $totalQuotes, $content, 'quotes');
		
		return Response::json($data, 200, [], JSON_NUMERIC_CHECK);
	}

	public function indexByApprovedQuotes($quote_approved_type, $user_id)
	{
		$page = $this->getPage();
		$this->relationInvolved = 'users';
		$pagesize = $this->getPagesize();

		$user = $this->userRepo->getById($user_id);
		
		// Handle user not found
		if (is_null($user)) {
			$data = [
				'status' => 'user_not_found',
				'error'  => "The user #".$user_id." was not found.",
			];

			return Response::json($data, 400);
		}
		
		// Get quotes
		$content = $this->getQuotesByApprovedForUser($page, $pagesize, $user, $quote_approved_type);

		// Handle no quotes found
		$totalQuotes = 0;
		if (is_null($content) OR empty($content) OR $content->count() == 0)
			throw new ApiNotFoundException('quotes');

		$totalQuotes = $this->quoteRepo->countQuotesByApprovedForUser($quote_approved_type, $user);

		$data = self::paginateContent($page, $pagesize, $totalQuotes, $content, 'quotes');
		
		return Response::json($data, 200, [], JSON_NUMERIC_CHECK);
	}

	public function index($random = null)
	{
		$page = $this->getPage();
		$pagesize = $this->getPagesize();

		$totalQuotes = $this->quoteRepo->totalPublished();

		// Get quotes
		if (is_null($random))
			$content = $this->getQuotesHome($page, $pagesize);
		else
			$content = $this->getQuotesRandom($page, $pagesize);

		// Handle no quotes found
		if (is_null($content) OR $content->count() == 0)
			throw new ApiNotFoundException('quotes');

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
		$content = $this->getQuotesSearch($page, $pagesize, $query);

		// Handle no quotes found
		$totalQuotes = 0;
		if (is_null($content) OR empty($content) OR $content->count() == 0)
			throw new ApiNotFoundException('quotes');

		$totalQuotes = $this->quoteRepo->searchCountPublishedWithQuery($query);

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
			$quotesSubmittedToday = $this->quoteRepo->submittedTodayForUser($user);
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
		$quote = $this->quoteRepo->createQuoteForUser($user, $content);

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

	public function getQuotesSearch($page, $pagesize, $query)
	{
		return $this->quoteRepo->searchPublishedWithQuery($query, $page, $pagesize);
	}

	private function getDefaultNbQuotesPerPage()
	{
		return Config::get('app.quotes.nbQuotesPerPage');
	}

	private function getQuotesHome($page, $pagesize)
	{
		return $this->quoteRepo->index($page, $pagesize);
	}

	private function getQuotesRandom($page, $pagesize)
	{
		return $this->quoteRepo->indexRandom($page, $pagesize);
	}

	private function getQuotesFavorite($page, $pagesize, $arrayIDFavoritesQuotesForUser)
	{
		return $this->quoteRepo->getForIds($arrayIDFavoritesQuotesForUser, $page, $pagesize);
	}
	
	private function getQuotesByApprovedForUser($page, $pagesize, $user, $quote_approved_type)
	{
		return $this->quoteRepo->getQuotesByApprovedForUser($user, $quote_approved_type, $page, $pagesize);
	}
}