<?php namespace TeenQuotes\Quotes\Controllers;

use App, Auth, BaseController, Input, Lang, Paginator, Redirect, Response;
use Route, Session, URL, Validator, View;
use TeenQuotes\Exceptions\QuoteNotFoundException;
use TeenQuotes\Http\JsonResponse;
use TeenQuotes\Quotes\Models\Quote;
use TeenQuotes\Quotes\Repositories\QuoteRepository;

class QuotesController extends BaseController {

	/**
	 * The API controller
	 * @var \TeenQuotes\Api\V1\Controllers\QuotesController
	 */
	private $api;

	/**
	 * @var \TeenQuotes\Quotes\Repositories\QuoteRepository
	 */
	private $quoteRepo;

	/**
	 * @var \TeenQuotes\Quotes\Validation\QuoteValidator
	 */
	private $quoteValidator;

	public function __construct(QuoteRepository $quoteRepo)
	{
		$this->beforeFilter('auth', ['on' => 'store']);
		$this->api = App::make('TeenQuotes\Api\V1\Controllers\QuotesController');
		$this->quoteRepo = $quoteRepo;
		$this->quoteValidator = App::make('TeenQuotes\Quotes\Validation\QuoteValidator');
	}

	/**
	 * Redirect to the new URL schema
	 *
	 * @param  int $id ID of the quote
	 * @return \Response
	 */
	public function redirectOldUrl($id)
	{
		return Redirect::route('quotes.show', $id, 301);
	}

	public function redirectTop()
	{
		return Redirect::route('quotes.top.favorites', null, 301);
	}

	/**
	 * Display last quotes
	 *
	 * @return \Response
	 */
	public function index()
	{
		$response = $this->retrieveLastQuotes();

		return $this->buildIndexResponse('quotes.index', $response);
	}

	/**
	 * Display random quotes
	 *
	 * @return \Response
	 */
	public function random()
	{
		$response = $this->retrieveRandomQuotes();

		return $this->buildIndexResponse('quotes.index', $response);
	}

	/**
	 * Display top favorited quotes
	 *
	 * @return \Response
	 */
	public function topFavorites()
	{
		$response = $this->retrieveTopFavorites();

		return $this->buildIndexResponse('quotes.top.favorites', $response);
	}

	/**
	 * Display top commented quotes
	 *
	 * @return \Response
	 */
	public function topComments()
	{
		$response = $this->retrieveTopComments();

		return $this->buildIndexResponse('quotes.top.comments', $response);
	}

	/**
	 * Index quotes for a given tag name
	 *
	 * @param  string $tagName The name of the tag
	 * @return \Response
	 */
	public function indexForTag($tagName)
	{
		$response = $this->retrieveQuotesForTag($tagName);

		return $this->buildIndexResponse('quotes.tags.index', $response);
	}

	/**
	 * Store a new quote in storage
	 *
	 * @return \Response
	 */
	public function store()
	{
		$user = Auth::user();

		$data = [
			'content'              => Input::get('content'),
			'quotesSubmittedToday' => $this->quoteRepo->submittedTodayForUser($user),
		];

		$this->quoteValidator->validatePosting($data);

		// Call the API to store the quote
		$response = $this->api->store(false);
		if ($response->getStatusCode() == 201)
			return Redirect::route('home')->with('success', Lang::get('quotes.quoteAddedSuccessfull', ['login' => $user->login]));

		App::abort(500, "Can't create quote.");
	}

	/**
	 * Display the form to add a quote
	 *
	 * @return \Response
	 */
	public function create()
	{
		$data = [
			'pageTitle'       => Lang::get('quotes.addquotePageTitle'),
			'pageDescription' => Lang::get('quotes.addquotePageDescription'),
		];

		return View::make('quotes.addquote', $data);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return \Response
	 */
	public function show($id)
	{
		$response = $this->api->show($id);

		$this->guardAgainstNotFound($response);

		$quote = $response->getOriginalData();

		// If the user was not logged in, we store the current URL in its session
		// After sign in / sign up, he will be redirected here
		if (Auth::guest())
			Session::put('url.intended', URL::route('quotes.show', $id));

		$data = [
			'quote'           => $quote,
			'pageTitle'       => Lang::get('quotes.singleQuotePageTitle', compact('id')),
			'pageDescription' => $quote->content,
		];

		// JS variables and colors are set in a view composer

		return View::make('quotes.show', $data);
	}

	public function getDataFavoritesInfo()
	{
		$quote = $this->quoteRepo->getById(Input::get('id'));
		$data = $quote->present()->favoritesData;

		$translate = Lang::choice('quotes.favoritesText', $data['nbFavorites'], $data);

		return Response::json(compact('translate'), 200);
	}

	private function buildIndexResponse($viewName, $response)
	{
		$quotes = $response['quotes'];

		$data = [
			'quotes'          => $quotes,
			'pageTitle'       => Lang::get('quotes.'.$this->cleanLangKey(Route::currentRouteName().'PageTitle')),
			'pageDescription' => Lang::get('quotes.'.$this->cleanLangKey(Route::currentRouteName().'PageDescription')),
			'paginator'       => Paginator::make($quotes->toArray(), $response['total_quotes'], $response['pagesize']),
		];

		return View::make($viewName, $data);
	}

	private function cleanLangKey($key)
	{
		return lcfirst(str_replace(' ', '', ucwords(str_replace('.', ' ', $key))));
	}

	private function retrieveTopFavorites()
	{
		$apiResponse = $this->api->getTopFavoritedQuotes();

		$this->guardAgainstNotFound($apiResponse);

		return $apiResponse->getOriginalData();
	}

	private function retrieveTopComments()
	{
		$apiResponse = $this->api->getTopCommentedQuotes();

		$this->guardAgainstNotFound($apiResponse);

		return $apiResponse->getOriginalData();
	}

	private function retrieveLastQuotes()
	{
		$apiResponse = $this->api->index();

		$this->guardAgainstNotFound($apiResponse);

		return $apiResponse->getOriginalData();
	}

	private function retrieveRandomQuotes()
	{
		$apiResponse = $this->api->random();

		$this->guardAgainstNotFound($apiResponse);

		return $apiResponse->getOriginalData();
	}

	private function retrieveQuotesForTag($tagName)
	{
		$apiResponse = $this->api->getQuotesForTag($tagName);

		$this->guardAgainstNotFound($apiResponse);

		return $apiResponse->getOriginalData();
	}

	/**
	 * Throw an exception if the given response is a not found response
	 *
	 * @param  JsonResponse $response the response
	 * @return void|\TeenQuotes\Exceptions\QuoteNotFoundException
	 */
	private function guardAgainstNotFound(JsonResponse $response)
	{
		if ($this->responseIsNotFound($response))
			throw new QuoteNotFoundException;
	}
}