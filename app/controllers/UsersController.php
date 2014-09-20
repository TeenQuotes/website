<?php

use TeenQuotes\Api\V1\Controllers\UsersController as UsersAPIController;

class UsersController extends \BaseController {

	public function __construct()
	{
		$this->beforeFilter('guest', array('only' => 'store'));
		$this->beforeFilter('auth', array('only' => array('edit', 'update', 'putPassword', 'putSettings')));
	}

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

	public function postLoginValidator()
	{

		$data = [
			'login' => Input::get('login')
		];

		$validator = Validator::make($data, ['login' => User::$rulesSignup['login']]);
		if ($validator->fails()) {
			$data = [
				'success' => false,
				'message' => $validator->messages()->first('login')
			];

			return Response::json($data);
		}

		$data = [
			'success' => true,
			'message' => $data['login'].'? '.Lang::get('auth.loginAwesome')
		];

		return Response::json($data);
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

			// Call the API - skip the API validator
			$response = App::make('TeenQuotes\Api\V1\Controllers\UsersController')->postUsers(false);
			if ($response->getStatusCode() == 201) {
				if (Session::has('url.intended'))
					return Redirect::intended('/')->with('success', Lang::get('auth.signupSuccessfull', array('login' => $data['login'])));
				else
					return Redirect::route('users.show', $data['login'])->with('success', Lang::get('auth.signupSuccessfull', array('login' => $data['login'])));
			}

			return Redirect::route('signup')->withErrors($validator)->withInput(Input::except('password'));
		}

		// Something went wrong.
		return Redirect::route('signup')->withErrors($validator)->withInput(Input::except('password'));
	}

	/**
	 * Display the specified resource.
	 *
	 * @param string $user_id The id or the login of the user
	 * @param string $type If it's not false, it could be 'favorites' or 'comments'
	 * @return Response
	 */
	public function show($user_id, $type = false)
	{
		// Page number for quotes
		$pageNumber = Input::get('page', 1);

		// Get the user
		$user = User::where('login', $user_id)->orWhere('id', $user_id)->first();

		if (is_null($user))
			throw new UserNotFoundException;

		// Throw an exception if the user has an hidden profile
		// We do not throw this exception if the user is currently
		// viewing its own hidden profile
		$viewingSelfProfile = (Auth::check() AND Auth::user()->login == $user->login);
		if ($user->isHiddenProfile() AND !$viewingSelfProfile)
			throw new HiddenProfileException;

		// Check where we can redirect the user
		$publishPossible   = $user->hasPublishedQuotes();
		$favoritesPossible = $user->hasFavoriteQuotes();
		$totalComments     = $user->getTotalComments();
		$commentsPossible  = ($totalComments > 0);
		
		// Check if we have content to display
		// If we have nothing to show, try to redirect somewhere else
		switch ($type) {
			case 'favorites':
				if (!$favoritesPossible) {
					if ($publishPossible)
						return Redirect::route('users.show', $user->login);
					if ($commentsPossible)
						return Redirect::route('users.show', [$user->login, 'comments']);

					$redirectFailed = true;
				}
				break;

			case 'comments':
				if (!$commentsPossible) {
					if ($publishPossible)
						return Redirect::route('users.show', $user->login);
					if ($favoritesPossible)
						return Redirect::route('users.show', [$user->login, 'favorites']);

					$redirectFailed = true;
				}
				break;

			// Asked for published quotes
			default:
				if (!$publishPossible) {
					if ($favoritesPossible)
						return Redirect::route('users.show', [$user->login, 'favorites']);
					if ($commentsPossible)
						return Redirect::route('users.show', [$user->login, 'comments']);
				}
		}

		// We failed to redirect, we will redirect to the default page
		if (isset($redirectFailed) AND $redirectFailed)
			return Redirect::route('users.show', $user->login);


		// Build the data array
		// Keys: quotes, paginator, colors, type
		if ($type == 'favorites')
			$data = self::dataShowFavoriteQuotes($user, $pageNumber);
		elseif ($type == 'comments')
			$data = self::dataShowComments($user, $pageNumber);
		else
			$data = self::dataShowPublishedQuotes($user, $pageNumber);

		$data['user']                 = $user;
		$data['pageTitle']            = Lang::get('users.profilePageTitle', array('login' => $user->login));
		$data['pageDescription']      = Lang::get('users.profilePageDescription', array('login' => $user->login));
		$data['hideAuthorQuote']      = ($data['type'] == 'published');
		$data['commentsCount']        = $totalComments;
		$data['addedFavCount']        = $user->getAddedFavCount();
		$data['quotesPublishedCount'] = $user->getPublishedQuotesCount();
		$data['favCount']             = $user->getFavoriteCount();
		$data['viewingSelfProfile']   = $viewingSelfProfile;
		// Used for deep linking in ProfileComposer
		$data['showType']             = ($type === false) ? 'published' : $type;

		// If the user is new and is viewing its own profile, a small welcome tutorial
		if (empty($data['quotes']) AND $viewingSelfProfile)
			return View::make('users.welcome', $data);

		return View::make('users.show', $data);
	}

	private static function dataShowFavoriteQuotes(User $user, $pageNumber)
	{
		// Time to store quotes in cache
		$expiresAt = Carbon::now()->addMinutes(10);

		// Get the list of favorite quotes
		$arrayIDFavoritesQuotesForUser = $user->arrayIDFavoritesQuotes();

		// Fetch the quotes
		$quotes = Cache::remember(User::$cacheNameForFavorited.$user->id.'_'.$pageNumber, $expiresAt, function() use ($user, $arrayIDFavoritesQuotesForUser)
		{
			return Quote::whereIn('id', $arrayIDFavoritesQuotesForUser)
				->with('user')
				->orderBy(DB::raw("FIELD(id, ".implode(',', $arrayIDFavoritesQuotesForUser).")"))
				->paginate(Config::get('app.users.nbQuotesPerPage'))
				->getItems();
		});

		// Build the associated paginator
		$paginator = Paginator::make($quotes, count($arrayIDFavoritesQuotesForUser), Config::get('app.users.nbQuotesPerPage'));
		// FIXME: could be prettier
		$paginator->setBaseUrl('/users/'.$user->login.'/favorites');

		// Build the associative array  #quote->id => "color"
		$IDsQuotes = array();
		foreach ($quotes as $quote)
			$IDsQuotes[] = $quote->id;
		
		// Store it in session
		$colors = Quote::storeQuotesColors($IDsQuotes);

		// Fix the type of quotes we will display
		$type = 'favorites';

		return [
			'quotes'    => $quotes,
			'paginator' => $paginator,
			'colors'    => $colors,
			'type'      => $type,
		];
	}

	private static function dataShowComments(User $user, $pageNumber)
	{
		$comments = $user
			->comments()
			->with('user', 'quote')
			->orderDescending()
			->paginate(Config::get('app.users.nbQuotesPerPage'))
			->getItems();

		// Build the associated paginator
		$paginator = Paginator::make($comments, $user->getTotalComments(), Config::get('app.users.nbQuotesPerPage'));
		// FIXME: could be prettier
		$paginator->setBaseUrl('/users/'.$user->login.'/comments');

		// Fix the type of quotes we will display
		$type = 'comments';

		return [
			'quotes'    => $comments,
			'paginator' => $paginator,
			'type'      => $type,
		];
	}

	private static function dataShowPublishedQuotes(User $user, $pageNumber)
	{
		// Time to store quotes in cache
		$expiresAt = Carbon::now()->addMinutes(10);

		// Fetch the quotes
		$quotes = Cache::remember(User::$cacheNameForPublished.$user->id.'_'.$pageNumber, $expiresAt, function() use ($user)
		{
			return Quote::forUser($user)
				->published()
				->orderDescending()
				->paginate(Config::get('app.users.nbQuotesPerPage'))
				->getItems();
		});

		$numberQuotesPublishedForUser = Cache::remember(User::$cacheNameForNumberQuotesPublished.$user->id, $expiresAt, function() use ($user)
		{
			return Quote::forUser($user)
				->published()
				->count();
		});

		// Build the associated paginator
		$paginator = Paginator::make($quotes, $numberQuotesPublishedForUser, Config::get('app.users.nbQuotesPerPage'));

		// Colors that will be used for quotes
		// Build the associative array  #quote->id => "color"
		$IDsQuotes = array();
		foreach ($quotes as $quote)
			$IDsQuotes[] = $quote->id;
		
		// Store it in session
		$colors = Quote::storeQuotesColors($IDsQuotes, $user->getColorsQuotesPublished());

		// Fix the type of quotes we will display
		$type = 'published';

		return [
			'quotes'    => $quotes,
			'paginator' => $paginator,
			'colors'    => $colors,
			'type'      => $type,
		];
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  string $id The login or the ID of the user
	 * @throws UserNotFoundException
	 * @return Response
	 */
	public function edit($id)
	{
		$user = User::whereLogin($id)->orWhere('id', $id)->first();

		if (is_null($user) OR $user->login != Auth::user()->login)
			throw new UserNotFoundException;
		else {

			// The color for published quotes
			$confColor = Setting::
				where('user_id', '=', $user->id)
				->where('key', '=', 'colorsQuotesPublished')
				->first();

			// Set the default color
			if (is_null($confColor))
				$selectedColor = Config::get('app.users.defaultColorQuotesPublished');
			else
				$selectedColor = $confColor->value;

			// Create an array like
			// ['blue' => 'Blue', 'red' => 'Red']
			$colorsInConf = Config::get('app.users.colorsAvailableQuotesPublished');
			$func = function ($colorName) {
				return Lang::get('colors.'.$colorName);
			};
			$colorsAvailable = array_combine($colorsInConf, array_map($func, $colorsInConf));

			$listCountries = Country::lists('name', 'id');

			// If the user hasn't filled its country yet we will try to auto-detect it
			// If it's not possible, we will fall back to the most common country: the USA
			$selectedCountry = is_null($user->country) ? UsersAPIController::detectCountry() : $selectedCountry = $user->country;

			// If the user hasn't filled its city yet we will try to auto-detect it
			if (empty(Input::old('city')) AND is_null($user->city))
				$selectedCity = UsersAPIController::detectCity();
			else
				$selectedCity = Input::old('city');

			$data = [
				'gender'           => $user->gender,
				'listCountries'    => $listCountries,
				'selectedCountry'  => $selectedCountry,
				'selectedCity'     => $selectedCity,
				'user'             => $user,
				'selectedColor'    => $selectedColor,
				'colorsAvailable'  => $colorsAvailable,
				'pageTitle'        => Lang::get('users.editPageTitle'),
				'pageDescription'  => Lang::get('users.editPageDescription'),
				'weeklyNewsletter' => $user->isSubscribedToNewsletter('weekly'),
				'dailyNewsletter'  => $user->isSubscribedToNewsletter('daily'),
			];

			return View::make('users.edit', $data);
		}
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  string $id The login or the ID of the user
	 * @return Response
	 */
	public function update($id)
	{
		$data = [
			'gender'    => Input::get('gender'),
			'birthdate' => Input::get('birthdate'),
			'country'   => Input::get('country'),
			'city'      => Input::get('city'),
			'about_me'  => Input::get('about_me'),
			'avatar'    => Input::file('avatar'),
		];

		$validator = Validator::make($data, User::$rulesUpdate);

		if ($validator->passes()) {
			
			// Call the API
			$response = App::make('TeenQuotes\Api\V1\Controllers\UsersController')->putProfile(false);
			if ($response->getStatusCode() == 200)
				return Redirect::back()->with('success', Lang::get('users.updateProfileSuccessfull', array('login' => Auth::user()->login)));

			App::abort(500, "Can't update profile");
		}

		// Something went wrong.
		return Redirect::back()->withErrors($validator)->withInput(Input::except('avatar'));
	}

	/**
	 * Update the password in storage
	 *
	 * @param  string $id The login or the ID of the user
	 * @return Response
	 */
	public function putPassword($id)
	{
		$data = [
			'password'              => Input::get('password'),
			'password_confirmation' => Input::get('password_confirmation'),
		];

		$validator = Validator::make($data, User::$rulesUpdatePassword);

		if ($validator->passes()) {
			$user = User::whereLogin($id)->orWhere('id', $id)->first();
			if ($user->login != Auth::user()->login)
				App::abort(401, 'Refused');
			$user->password = Hash::make($data['password']);
			$user->save();

			return Redirect::back()->with('success', Lang::get('users.updatePasswordSuccessfull', array('login' => $user->login)));
		}

		// Something went wrong.
		return Redirect::to(URL::route('users.edit', Auth::user()->login)."#edit-password")->withErrors($validator)->withInput(Input::all());
	}

	/**
	 * Update settings for the user
	 *
	 * @param  string $id The login or the ID of the user
	 * @return Response
	 */
	public function putSettings($id)
	{
		$user = User::whereLogin($id)->orWhere('id', $id)->first();
		if ($user->login != Auth::user()->login)
			App::abort(401, 'Refused');
		
		$response = App::make('TeenQuotes\Api\V1\Controllers\UsersController')->putSettings($user);

		// Handle error
		if ($response->getStatusCode() == 400) {
			$json = json_decode($response->getContent());
			// If the color was wrong
			if ($json->status == 'wrong_color')
				return Redirect::back()->with('warning', Lang::get('users.colorNotAllowed'));
		}

		if ($response->getStatusCode() == 200)
			return Redirect::back()->with('success', Lang::get('users.updateSettingsSuccessfull', array('login' => $user->login)));
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @return Response
	 */
	public function destroy()
	{
		$data = [
			'password'            => Input::get('password'),
			'delete-confirmation' => Input::get('delete-confirmation'),
			'login'               => Auth::user()->login
		];

		// We will use a custom messagefor the delete confirmation input
		$messages = [
    		'delete-confirmation.in' => Lang::get('users.writeDeleteHere'),
		];

		$validator = Validator::make($data, User::$rulesDestroy, $messages);

		if ($validator->fails())
			return Redirect::to(URL::route('users.edit', Auth::user()->login)."#delete-account")->withErrors($validator)->withInput(Input::except('password'));
		else {
			unset($data['delete-confirmation']);
			if (!Auth::validate($data))
				return Redirect::to(URL::route('users.edit', Auth::user()->login)."#delete-account")->withErrors(array('password' => Lang::get('auth.passwordInvalid')))->withInput(Input::except('password'));
		}

		// Delete the user
		User::find(Auth::id())->delete();
		// Log him out
		Auth::logout();

		return Redirect::home()->with('success', Lang::get('users.deleteAccountSuccessfull'));
	}

}