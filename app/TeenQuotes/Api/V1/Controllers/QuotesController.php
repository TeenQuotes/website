<?php namespace TeenQuotes\Api\V1\Controllers;

use App, Config, Input;
use Laracasts\Validation\FormValidationException;
use TeenQuotes\Api\V1\Interfaces\PaginatedContentInterface;
use TeenQuotes\Exceptions\ApiNotFoundException;
use TeenQuotes\Http\Facades\Response;
use TeenQuotes\Quotes\Models\Quote;

class QuotesController extends APIGlobalController implements PaginatedContentInterface {

	private $relationInvolved = 'quotes';

	/**
	 * @var TeenQuotes\Quotes\Validation\QuoteValidator
	 */
	private $quoteValidator;

	protected function bootstrap()
	{
		$this->quoteValidator = App::make('TeenQuotes\Quotes\Validation\QuoteValidator');
	}

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
		$this->relationInvolved = 'users';

		$user = $this->userRepo->getById($user_id);

		// Handle user not found
		if (is_null($user))
		{
			$data = [
				'status' => 'user_not_found',
				'error'  => "The user #".$user_id." was not found.",
			];

			return Response::json($data, 400);
		}

		// Get the list of favorite quotes
		$quotesFavorited = $this->favQuoteRepo->quotesFavoritesForUser($user);

		$total = count($quotesFavorited);

		$quotes = $this->getQuotesFavorite($this->getPage(), $this->getPagesize(), $quotesFavorited);

		return $this->buildPaginatedResponse($quotes, $total);
	}

	public function indexByApprovedQuotes($quote_approved_type, $user_id)
	{
		$this->relationInvolved = 'users';

		$user = $this->userRepo->getById($user_id);

		// Handle user not found
		if (is_null($user))
		{
			$data = [
				'status' => 'user_not_found',
				'error'  => "The user #".$user_id." was not found.",
			];

			return Response::json($data, 400);
		}

		$quotes = $this->getQuotesByApprovedForUser($this->getPage(), $this->getPagesize(), $user, $quote_approved_type);

		$total = $this->quoteRepo->countQuotesByApprovedForUser($quote_approved_type, $user);

		return $this->buildPaginatedResponse($quotes, $total);
	}

	public function index()
	{
		$quotes = $this->getQuotesHome($this->getPage(), $this->getPagesize());

		$total = $this->quoteRepo->totalPublished();

		return $this->buildPaginatedResponse($quotes, $total);
	}

	public function random()
	{
		$quotes = $this->getQuotesRandom($this->getPage(), $this->getPagesize());

		$total = $this->quoteRepo->totalPublished();

		return $this->buildPaginatedResponse($quotes, $total);
	}

	public function getTopFavoritedQuotes()
	{
		$ids = $this->favQuoteRepo->getTopQuotes($this->getPage(), $this->getPagesize());
		$quotes = $this->quoteRepo->getForIds($ids, 1, count($ids));

		$total = $this->quoteRepo->nbQuotesWithFavorites();

		return $this->buildPaginatedResponse($quotes, $total);
	}

	public function getTopCommentedQuotes()
	{
		$ids = $this->commentRepo->getTopQuotes($this->getPage(), $this->getPagesize());
		$quotes = $this->quoteRepo->getForIds($ids, 1, count($ids));

		$total = $this->quoteRepo->nbQuotesWithComments();

		return $this->buildPaginatedResponse($quotes, $total);
	}

	public function getSearch($query)
	{
		$quotes = $this->getQuotesSearch($this->getPage(), $this->getPagesize(), $query);

		$total = $this->quoteRepo->searchCountPublishedWithQuery($query);

		return $this->buildPaginatedResponse($quotes, $total);
	}

	public function store($doValidation = true)
	{
		$user = $this->retrieveUser();
		$content = Input::get('content');
		$quotesSubmittedToday = $this->quoteRepo->submittedTodayForUser($user);

		if ($doValidation)
		{
			try {
				$this->quoteValidator->validateNbSubmittedToday(compact('quotesSubmittedToday'));
			}
			catch (FormValidationException $e)
			{
				$errors = [
					'status' => 'too_much_submitted_quotes',
					'error'  => "The maximum number of quotes you can submit is 5 per day."
				];

				return Response::json($errors, 400);
			}

			try {
				$this->quoteValidator->validatePosting(compact('content', 'quotesSubmittedToday'));
			}
			catch (FormValidationException $e)
			{
				$errors = [
					'status' => 'wrong_content',
					'error'  => 'Content of the quote should be between 50 and 300 characters.'
				];

				return Response::json($errors, 400);
			}
		}

		// Store the quote
		$quote = $this->quoteRepo->createQuoteForUser($user, $content);

		return Response::json($quote, 201, [], JSON_NUMERIC_CHECK);
	}

	public function getPagesize()
	{
		switch ($this->relationInvolved)
		{
			case 'users':
				return Input::get('pagesize', Config::get('app.users.nbQuotesPerPage'));

			case 'quotes':
				return Input::get('pagesize', $this->getDefaultNbQuotesPerPage());
		}
	}

	/**
	 * Build a paginated response for quotes
	 * @param  Illuminate\Database\Eloquent\Collection $quotes
	 * @param  int $total
	 * @throws TeenQuotes\Exceptions\ApiNotFoundException If no quotes were found
	 * @return TeenQuotes\Http\Facades\Response
	 */
	private function buildPaginatedResponse($quotes, $total)
	{
		// Handle no quotes found
		if (is_null($quotes) OR $quotes->count() == 0)
			throw new ApiNotFoundException('quotes');

		$data = self::paginateContent($this->getPage(), $this->getPagesize(), $total, $quotes, 'quotes');

		return Response::json($data, 200, [], JSON_NUMERIC_CHECK);
	}

	private function getQuotesSearch($page, $pagesize, $query)
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

	private function getQuotesFavorite($page, $pagesize, $quotesFavorited)
	{
		return $this->quoteRepo->getForIds($quotesFavorited, $page, $pagesize);
	}

	private function getQuotesByApprovedForUser($page, $pagesize, $user, $quote_approved_type)
	{
		return $this->quoteRepo->getQuotesByApprovedForUser($user, $quote_approved_type, $page, $pagesize);
	}
}