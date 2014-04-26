<?php

class UsersController extends \BaseController {

	public function __construct()
	{
		$this->beforeFilter('guest', array('on' => 'store'));
	}

	protected static $nbQuotesPerPage = 5;

	/**
	 * Displays the signup form
	 *
	 * @return Response
	 */
	public function getSignup()
	{
		$data = [
			'pageTitle'       => Lang::get('auth.signupPageTitle'),
			'pageDescription' => Lang::get('auth.signupPageDescription'),
		];

		return View::make('auth.signup', $data);
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		//
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
		$data = [
			'login'    => Input::get('login'),
			'password' => Input::get('password'),
			'email'    => Input::get('email'),
		];

		$validator = Validator::make($data, User::$rulesSignup);

		// Check if the form validates with success.
		if ($validator->passes()) {
			
			// Store the user
			$user = new User;
			$user->login = $data['login'];
			$user->email = $data['email'];
			$user->password = Hash::make($data['password']);
			$user->ip = $_SERVER['REMOTE_ADDR'];
			$user->last_visit = Carbon::now()->toDateTimeString();
			$user->save();

			// Log the user
			Auth::login($user);

			// TODO : send an email
			
			return Redirect::intended('/')->with('success', Lang::get('auth.signupSuccessfull', array('login' => $data['login'])));
		}

		// Something went wrong.
		return Redirect::route('signup')->withErrors($validator)->withInput(Input::except('password'));
	}

	/**
	 * Display the specified resource.
	 *
	 * @param string $user_id The id or the login of the user
	 * @param string $fav If it's not false, we will display the favorite quotes of the
	 * @return Response
	 */
	public function show($user_id, $fav = false)
	{
		// Page number for quotes
		$pageNumber = Input::get('page', 1);

		// Time to store quotes in cache
		$expiresAt = Carbon::now()->addMinutes(10);
		
		// Get the user
		$user = User::where('login', $user_id)->orWhere('id', $user_id)->first();

		// TODO: handle this error
		if (is_null($user))
			App::abort(404, 'User not found');

		// ---- We want to display the favorites quotes of the user
		if ($fav != false) {
			
			$arrayIDFavoritesQuotesForUser = Cache::remember(FavoriteQuote::$cacheNameFavoritesForUser.$user->id, $expiresAt, function() use ($user)
			{
				return FavoriteQuote::forUser($user)->select('quote_id')->get()->lists('quote_id');
			});

			// Fetch the quotes
			$quotes = Cache::remember(User::$cacheNameForFavorited.$user->id.'_'.$pageNumber, $expiresAt, function() use ($user, $arrayIDFavoritesQuotesForUser)
			{
				return Quote::whereIn('id', $arrayIDFavoritesQuotesForUser)
					->with('user')
					->orderDescending()
					->paginate(self::$nbQuotesPerPage)
					->getItems();
			});

			// Build the associated paginator
			$paginator = Paginator::make($quotes, count($arrayIDFavoritesQuotesForUser), self::$nbQuotesPerPage);
			// FIXME: could be prettier
			$paginator->setBaseUrl('/users/'.$user->login.'/fav');

			// Fix the type of quotes we will display
			$type = 'favorites';
		}
		// ---- We want to display the published quotes of the user
		else {
			// Fetch the quotes
			$quotes = Cache::remember(User::$cacheNameForPublished.$user->id.'_'.$pageNumber, $expiresAt, function() use ($user)
			{
				return Quote::forUser($user)
					->published()
					->orderDescending()
					->paginate(self::$nbQuotesPerPage)
					->getItems();
			});

			$numberQuotesPublishedForUser = Cache::remember(User::$cacheNameForNumberQuotesPublished.$user->id, $expiresAt, function() use ($user)
			{
				return Quote::forUser($user)
					->published()
					->count();
			});

			// Build the associated paginator
			$paginator = Paginator::make($quotes, $numberQuotesPublishedForUser, self::$nbQuotesPerPage);

			// Fix the type of quotes we will display
			$type = 'published';
		}
		
		$data = [
			'quotes'          => $quotes,
			'colors'          => Quote::getRandomColors(true),
			'pageTitle'       => Lang::get('quotes.'.Route::currentRouteName().'PageTitle'),
			'pageDescription' => Lang::get('quotes.'.Route::currentRouteName().'PageDescription'),
			'paginator'       => $paginator,
			'type'            => $type,
		];

		return View::make('users.show', $data);
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