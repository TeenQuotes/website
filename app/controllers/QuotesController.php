<?php

class QuotesController extends \BaseController {

	public function __construct()
	{
		$this->beforeFilter('auth', array('on' => 'store'));
	}

	protected static $nbQuotesPerPage = 10;

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

		$numberQuotesPublished = Cache::remember(Quote::$cacheNameNumberPublished, $expiresAt, function()
		{
			return Quote::published()->count();
		});

		// Random quotes or not?
		if (Route::currentRouteName() != 'random') {
			$quotes = Cache::remember(Quote::$cacheNameQuotesPage.$pageNumber, $expiresAt, function()
			{
				return Quote::published()
					->with('user')
					->orderDescending()
					->paginate(self::$nbQuotesPerPage)
					->getItems();
			});
		}
		else {
			$quotes = Cache::remember(Quote::$cacheNameRandomPage.$pageNumber, $expiresAt, function()
			{
				return Quote::published()
					->with('user')
					->random()
					->paginate(self::$nbQuotesPerPage)
					->getItems();
			});
		}

		// FIXME: Handle not found
		if (is_null($quotes))
			return Response::view('errors.missing', array(), 404);

		$data = [
			'quotes'          => $quotes,
			'colors'          => Quote::getRandomColors(),
			'pageTitle'       => Lang::get('quotes.'.Route::currentRouteName().'PageTitle'),
			'pageDescription' => Lang::get('quotes.'.Route::currentRouteName().'PageDescription'),
			'paginator'       => Paginator::make($quotes, $numberQuotesPublished, self::$nbQuotesPerPage),
		];

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

			// Store the quote
			$quote = new Quote;
			$quote->content = $data['content'];

			$login = $user->login;
			$user->quotes()->save($quote);

			return Redirect::route('home')->with('success', Lang::get('quotes.quoteAddedSuccessfull', array('login' => $login)));
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
		$quote = Quote::find($id);

		// TODO Handle not found
		if (is_null($quote))
			return Response::view('errors.missing', array(), 404);

		// If the user was not logged in, we store the current URL in its session
		// After sign in / sign up, he will be redirected here
		if (Auth::guest())
			Session::put('url.intended', URL::route('quotes.show', $id));

		$data = [
			'quote'           => $quote,
			'colors'          => Quote::getRandomColors(),
			'comments'        => Comment::with('user')->where('quote_id', '=', $quote->id)->orderBy('created_at', 'asc')->get(),
			'pageTitle'       => Lang::get('quotes.singleQuotePageTitle', array('id' => $id)),
			'pageDescription' => $quote->content,
		];

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