<?php

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use TeenQuotes\Http\JsonResponse;

class QuotesController extends BaseController {

	/**
	 * The API controller
	 * @var TeenQuotes\Api\V1\Controllers\QuotesController
	 */
	private $api;

	public function __construct()
	{
		$this->beforeFilter('auth', ['on' => 'store']);
		$this->api = App::make('TeenQuotes\Api\V1\Controllers\QuotesController');
	}

	/**
	 * Redirect to the new URL type
	 * @param  int $id ID of the Quote
	 * @return Response
	 */
	public function redirectOldUrl($id)
	{
		return Redirect::route('quotes.show', $id, 301);
	}

	/**
	 * Display a bunch of quotes
	 *
	 * @return Response
	 */
	public function index()
	{		
		// Random quotes or not?
		$response = (Route::currentRouteName() == 'random') ? $this->retrieveRandomQuotes() : $this->retrieveLastQuotes();

		// Grab quotes
		$quotes = $response['quotes'];

		$data = [
			'quotes'          => $quotes,
			'pageTitle'       => Lang::get('quotes.'.Route::currentRouteName().'PageTitle'),
			'pageDescription' => Lang::get('quotes.'.Route::currentRouteName().'PageDescription'),
			'paginator'       => Paginator::make($quotes->toArray(), $response['total_quotes'], $response['pagesize']),
		];

		return View::make('quotes.index', $data);
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

	/**
	 * Throw an exception if the given response is a not found response
	 * @param  JsonResponse $response the response
	 * @return void|QuoteNotFoundException
	 */
	private function guardAgainstNotFound(JsonResponse $response)
	{
		if ($this->responseIsNotFound($response))
			throw new QuoteNotFoundException;
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$user = Auth::user();

		$data = [
			'content'              => Input::get('content'),
			'quotesSubmittedToday' => Quote::createdToday()->forUser($user)->count(),
		];

		$validator = Validator::make($data, Quote::$rulesAdd);

		// Check if the form validates with success.
		if ($validator->passes()) {

			// Call the API to store the quote
			$response = $this->api->store(false);
			if ($response->getStatusCode() == 201)
				return Redirect::route('home')->with('success', Lang::get('quotes.quoteAddedSuccessfull', ['login' => $user->login]));
			
			App::abort(500, "Can't create quote.");
		}

		// Something went wrong.
		return Redirect::route('addquote')->withErrors($validator)->withInput(Input::all());
	}

	/**
	 * Display the form to add a quote
	 *
	 * @return Response
	 */
	public function create()
	{
		$data = [
			'pageTitle'       => Lang::get('quotes.addquotePageTitle'),
			'pageDescription' => Lang::get('quotes.addquotePageDescription'),
		];

		// JS variables are set in a view composer

		return View::make('quotes.addquote', $data);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
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
		$quote = Quote::whereId(Input::get('id'))->firstOrFail();
		$data = $quote->present()->favoritesData;

		$translate = Lang::choice('quotes.favoritesText', $data['nbFavorites'], $data);

		return Response::json(compact('translate'), 200);
	}
}