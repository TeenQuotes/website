<?php

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class QuotesController extends \BaseController {

	public function __construct()
	{
		$this->beforeFilter('auth', ['on' => 'store']);
	}

	/**
	 * Redirect to the new URL type
	 * @param  int $id ID of the Quote
	 * @return \Response
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
		$quotes = (Route::currentRouteName() == 'random') ? $this->retrieveRandomQuotes() : $this->retrieveLastQuotes();

		// Transform quotes into a collection
		$quotes = new Collection($quotes);

		$data = [
			'quotes'          => $quotes,
			'pageTitle'       => Lang::get('quotes.'.Route::currentRouteName().'PageTitle'),
			'pageDescription' => Lang::get('quotes.'.Route::currentRouteName().'PageDescription'),
			'paginator'       => Paginator::make($quotes->toArray(), Quote::nbQuotesPublished(), Config::get('app.quotes.nbQuotesPerPage')),
		];

		return View::make('quotes.index', $data);
	}

	private function getPageAndExpireCacheTime()
	{
		// Page number for quotes
		$pageNumber = Input::get('page', 1);

		// Time to store quotes
		$expiresAt = Carbon::now()->addMinutes(1);

		return [$pageNumber, $expiresAt];
	}

	private function retrieveLastQuotes()
	{
		list($pageNumber, $expiresAt) = $this->getPageAndExpireCacheTime();
		
		$quotes = Cache::remember(Quote::$cacheNameQuotesPage.$pageNumber, $expiresAt, function()
		{
			return Quote::published()
				->with('user')
				->orderDescending()
				->paginate(Config::get('app.quotes.nbQuotesPerPage'))
				->getItems();
		});

		if (empty($quotes))
			throw new QuoteNotFoundException;

		return $quotes;
	}

	private function retrieveRandomQuotes()
	{
		list($pageNumber, $expiresAt) = $this->getPageAndExpireCacheTime();

		$quotes = Cache::remember(Quote::$cacheNameRandomPage.$pageNumber, $expiresAt, function()
		{
			return Quote::published()
				->with('user')
				->random()
				->paginate(Config::get('app.quotes.nbQuotesPerPage'))
				->getItems();
		});

		if (empty($quotes))
			throw new QuoteNotFoundException;

		return $quotes;
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		//
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
			$response = App::make('TeenQuotes\Api\V1\Controllers\QuotesController')->store(false);
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
	public function getAddQuote()
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
		try {
			$quote = Quote::whereId($id)
			->with('comments')
			->withSmallUser('comments.user')
			->withSmallUser('favorites.user')
			->withSmallUser()
			->firstOrFail();
		} catch (ModelNotFoundException $e) {
			throw new QuoteNotFoundException;
		}

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

		// Register the view in the recommendation engine
		$quote->registerViewAction();

		return View::make('quotes.show', $data);
	}

	public function getDataFavoritesInfo()
	{
		$quote = Quote::whereId(Input::get('id'))->firstOrFail();
		$data = $quote->present()->favoritesData;

		$translate = Lang::choice('quotes.favoritesText', $data['nbFavorites'], $data);

		return Response::json(compact('translate'), 200);
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

}