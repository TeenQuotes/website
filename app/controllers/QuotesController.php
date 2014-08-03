<?php

class QuotesController extends \BaseController {

	public function __construct()
	{
		$this->beforeFilter('auth', ['on' => 'store']);
	}

	/**
	 * Display a bunch of quotes
	 *
	 * @return Response
	 */
	public function index()
	{
		// Page number for quotes
		$pageNumber = Input::get('page', 1);

		// Time to store quotes
		$expiresAt = Carbon::now()->addMinutes(1);

		$numberQuotesPublished = Quote::nbQuotesPublished();
		
		// Random quotes or not?
		if (Route::currentRouteName() != 'random') {
			$quotes = Cache::remember(Quote::$cacheNameQuotesPage.$pageNumber, $expiresAt, function()
			{
				return Quote::published()
					->with('user')
					->orderDescending()
					->paginate(Config::get('app.quotes.nbQuotesPerPage'))
					->getItems();
			});
		}
		else {
			$quotes = Cache::remember(Quote::$cacheNameRandomPage.$pageNumber, $expiresAt, function()
			{
				return Quote::published()
					->with('user')
					->random()
					->paginate(Config::get('app.quotes.nbQuotesPerPage'))
					->getItems();
			});
		}

		if (is_null($quotes))
			throw new QuoteNotFoundException;

		// Build the associative array  #quote->id => "color"
		$IDsQuotes = array();
		foreach ($quotes as $quote)
			$IDsQuotes[] = $quote->id;
		
		// Store it in session
		$colors = Quote::storeQuotesColors($IDsQuotes);

		$data = [
			'quotes'          => $quotes,
			'colors'          => $colors,
			'pageTitle'       => Lang::get('quotes.'.Route::currentRouteName().'PageTitle'),
			'pageDescription' => Lang::get('quotes.'.Route::currentRouteName().'PageDescription'),
			'paginator'       => Paginator::make($quotes, $numberQuotesPublished, Config::get('app.quotes.nbQuotesPerPage')),
		];

		// The AdBlock disclaimer
		JavaScript::put([
			'moneyDisclaimer' => Lang::get('quotes.adblockDisclaimer'),
    	]);

		return View::make('quotes.index', $data);
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
			$response = App::make('TeenQuotes\Api\V1\Controllers\QuotesController')->postStoreQuote(false);
			if ($response->getStatusCode() == 200)
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

		// Put variables that we will use in JavaScript
		JavaScript::put([
			'contentShortHint' => Lang::get('quotes.contentShortHint'),
			'contentGreatHint' => Lang::get('quotes.contentGreatHint'),
			'eventCategory'    => 'addquote',
			'eventAction'      => 'logged-in',
			'eventLabel'       => 'addquote-page'
    	]);

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
		$quote = Quote::whereId($id)->published()->first();

		if (is_null($quote))
			throw new QuoteNotFoundException;

		// If the user was not logged in, we store the current URL in its session
		// After sign in / sign up, he will be redirected here
		if (Auth::guest())
			Session::put('url.intended', URL::route('quotes.show', $id));

		// Load colors for the quote
		if (Session::has('colors.quote') AND array_key_exists($id, Session::get('colors.quote')))
			$colors = Session::get('colors.quote');
		else {
			$colors = array();
			$colors[$id] = 'color-1';
		}

		$data = [
			'quote'           => $quote,
			'comments'        => Comment::with('user')->where('quote_id', '=', $quote->id)->orderBy('created_at', 'asc')->get(),
			'pageTitle'       => Lang::get('quotes.singleQuotePageTitle', array('id' => $id)),
			'pageDescription' => $quote->content,
			'colors'          => $colors,
		];

		// Put variables that we will use in JavaScript
		JavaScript::put([
			'contentShortHint' => Lang::get('comments.contentShortHint'),
			'contentGreatHint' => Lang::get('comments.contentGreatHint'),
    	]);

		return View::make('quotes.show', $data);
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