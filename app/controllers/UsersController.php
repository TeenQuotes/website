<?php

use TeenQuotes\Api\V1\Controllers\UsersController as UsersAPIController;

class UsersController extends \BaseController {

	private $usersAPIController;

	public function __construct()
	{
		$this->beforeFilter('guest', ['only' => 'store']);
		$this->beforeFilter('auth', ['only' => ['edit', 'update', 'putPassword', 'putSettings']]);
		
		$this->usersAPIController = App::make('TeenQuotes\Api\V1\Controllers\UsersController');
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
		
		if ($validator->fails())
			return Response::json([
				'success' => false,
				'message' => $validator->messages()->first('login')
			]);

		return Response::json([
			'success' => true,
			'message' => $data['login'].'? '.Lang::get('auth.loginAwesome')
		]);
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
			$response = $this->usersAPIController->store(false);
			if ($response->getStatusCode() == 201) {
				if (Session::has('url.intended'))
					return Redirect::intended('/')->with('success', Lang::get('auth.signupSuccessfull', ['login' => $data['login']]));
				else
					return Redirect::route('users.show', $data['login'])->with('success', Lang::get('auth.signupSuccessfull', ['login' => $data['login']]));
			}

			return Redirect::route('signup')->withErrors($validator)->withInput(Input::except('password'));
		}

		// Something went wrong
		return Redirect::route('signup')->withErrors($validator)->withInput(Input::except('password'));
	}

	private function redirectUserIfContentNotAvailable($user, $type)
	{
		// Check where we can redirect the user
		$publishPossible   = $user->hasPublishedQuotes();
		$favoritesPossible = $user->hasFavoriteQuotes();
		$commentsPossible  = $user->hasPostedComments();
		
		// Check if we have content to display
		// If we have nothing to show, try to redirect somewhere else
		$redirectFailed = false;
		switch ($type) {
			case 'favorites':
				if ( ! $favoritesPossible) {
					if ($publishPossible)
						return Redirect::route('users.show', $user->login);
					if ($commentsPossible)
						return Redirect::route('users.show', [$user->login, 'comments']);

					$redirectFailed = true;
				}
				break;

			case 'comments':
				if ( ! $commentsPossible) {
					if ($publishPossible)
						return Redirect::route('users.show', $user->login);
					if ($favoritesPossible)
						return Redirect::route('users.show', [$user->login, 'favorites']);

					$redirectFailed = true;
				}
				break;

			// Asked for published quotes
			case 'published':
				if ( ! $publishPossible) {
					if ($favoritesPossible)
						return Redirect::route('users.show', [$user->login, 'favorites']);
					if ($commentsPossible)
						return Redirect::route('users.show', [$user->login, 'comments']);
				}
		}

		// We failed to redirect, we will redirect to the default page
		if ($redirectFailed)
			return Redirect::route('users.show', $user->login);
	}

	private function userViewingSelfProfile($user)
	{
		return (Auth::check() AND Auth::user()->login == $user->login);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param string $user_id The login of the user
	 * @param string $type Can be 'favorites'|'comments'|'published'
	 * @return Response
	 */
	public function show($user_id, $type = 'published')
	{
		// Get the user
		$user = User::where('login', $user_id)->first();

		if (is_null($user))
			throw new UserNotFoundException;

		$this->redirectUserIfContentNotAvailable($user, $type);

		// Throw an exception if the user has an hidden profile
		// We do not throw this exception if the user is currently
		// viewing its own hidden profile
		if ($user->isHiddenProfile() AND ! $this->userViewingSelfProfile($user))
			throw new HiddenProfileException;

		// Build the data array. Keys: quotes, paginator
		switch ($type) {
			case 'favorites':
				$data = self::dataShowFavoriteQuotes($user);
				break;

			case 'comments':
				$data = self::dataShowComments($user);
				break;

			case 'published':
				$data = self::dataShowPublishedQuotes($user);
				break;
		}

		$data['user']               = $user;
		// Used for deep linking in ProfileComposer
		$data['type']               = $type;
		$data['viewingSelfProfile'] = $this->userViewingSelfProfile($user);
		$data['pageTitle']          = Lang::get('users.profilePageTitle', ['login' => $user->login]);
		$data['pageDescription']    = Lang::get('users.profilePageDescription', ['login' => $user->login]);

		// If the user is new and is viewing its own profile, a small welcome tutorial
		if (empty($data['quotes']) AND $this->userViewingSelfProfile($user))
			return View::make('users.welcome', $data);

		return View::make('users.show', $data);
	}

	private static function dataShowFavoriteQuotes(User $user)
	{
		$pageNumber = Input::get('page', 1);

		// Time to store quotes in cache
		$expiresAt = Carbon::now()->addMinutes(10);

		// Get the list of favorite quotes
		$arrayIDFavoritesQuotesForUser = $user->arrayIDFavoritesQuotes();

		// Fetch the quotes
		$quotes = Quote::whereIn('id', $arrayIDFavoritesQuotesForUser)
				->with('user')
				->orderBy(DB::raw("FIELD(id, ".implode(',', $arrayIDFavoritesQuotesForUser).")"))
				->paginate(Config::get('app.users.nbQuotesPerPage'))
				->getItems();

		// Build the associated paginator
		$paginator = Paginator::make($quotes, count($arrayIDFavoritesQuotesForUser), Config::get('app.users.nbQuotesPerPage'));
		$paginator->setBaseUrl(URL::route('users.show', [$user->login, 'favorites'], false));

		return compact('quotes', 'paginator');
	}

	private static function dataShowComments(User $user)
	{
		$pageNumber = Input::get('page', 1);

		$comments = $user->comments()
			->with('user', 'quote')
			->orderDescending()
			->paginate(Config::get('app.users.nbQuotesPerPage'))
			->getItems();

		// Build the associated paginator
		$paginator = Paginator::make($comments, $user->getTotalComments(), Config::get('app.users.nbQuotesPerPage'));
		$paginator->setBaseUrl(URL::route('users.show', [$user->login, 'comments'], false));

		return [
			'quotes'    => $comments,
			'paginator' => $paginator,
		];
	}

	private static function dataShowPublishedQuotes(User $user)
	{
		$pageNumber = Input::get('page', 1);

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

		$numberQuotesPublishedForUser = $user->getPublishedQuotesCount();

		// Build the associated paginator
		$paginator = Paginator::make($quotes, $numberQuotesPublishedForUser, Config::get('app.users.nbQuotesPerPage'));

		return compact('quotes', 'paginator');
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  string $id The login of the user
	 * @throws UserNotFoundException
	 * @return Response
	 */
	public function edit($id)
	{
		$user = User::whereLogin($id)->first();

		if (is_null($user) OR ! $this->userIsAllowedToEdit($user))
			throw new UserNotFoundException;

		// The color for published quotes
		$confColor = Setting::where('user_id', '=', $user->id)
			->where('key', '=', 'colorsQuotesPublished')
			->first();

		// Set the default color
		if (is_null($confColor))
			$selectedColor = Config::get('app.users.defaultColorQuotesPublished');
		else
			$selectedColor = $confColor->value;

		list($selectedCountry, $selectedCity) = $this->getCountryAndCity($user);

		$data = [
			'gender'           => $user->gender,
			'listCountries'    => Country::lists('name', 'id'),
			'selectedCountry'  => $selectedCountry,
			'selectedCity'     => $selectedCity,
			'user'             => $user,
			'selectedColor'    => $selectedColor,
			'pageTitle'        => Lang::get('users.editPageTitle'),
			'pageDescription'  => Lang::get('users.editPageDescription'),
		];

		return View::make('users.edit', $data);
	}

	/**
	 * Get country and city for a given user. If we have no information, try to guess it!
	 * @param  User $user The user model
	 * @return array The country and the city
	 */
	private function getCountryAndCity(User $user)
	{
		// If the user hasn't filled its country yet we will try to auto-detect it
		// If it's not possible, we will fall back to the most common country: the USA
		$selectedCountry = is_null($user->country) ? UsersAPIController::detectCountry() : $user->country;

		// If the user hasn't filled its city yet we will try to auto-detect it
		if (empty(Input::old('city')) AND is_null($user->city))
			$selectedCity = UsersAPIController::detectCity();
		else
			$selectedCity = Input::old('city');

		return [$selectedCountry, $selectedCity];
	}

	private function userIsAllowedToEdit($user)
	{
		return ($user->login == Auth::user()->login);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  string $id The login of the user
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
			$response = $this->usersAPIController->putProfile(false);
			if ($response->getStatusCode() == 200)
				return Redirect::back()->with('success', Lang::get('users.updateProfileSuccessfull', ['login' => Auth::user()->login]));

			App::abort(500, "Can't update profile");
		}

		// Something went wrong
		return Redirect::back()->withErrors($validator)->withInput(Input::except('avatar'));
	}

	/**
	 * Update the password in storage
	 *
	 * @param  string $id The login of the user
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
			$user = User::whereLogin($id)->first();
			if (! $this->userIsAllowedToEdit($user))
				App::abort(401, 'Refused');
			$user->password = Hash::make($data['password']);
			$user->save();

			return Redirect::back()->with('success', Lang::get('users.updatePasswordSuccessfull', ['login' => $user->login]));
		}

		// Something went wrong
		return Redirect::to(URL::route('users.edit', Auth::user()->login)."#edit-password")->withErrors($validator)->withInput(Input::all());
	}

	/**
	 * Update settings for the user
	 *
	 * @param  string $id The login of the user
	 * @return Response
	 */
	public function putSettings($id)
	{
		$user = User::whereLogin($id)->first();
		if ( ! $this->userIsAllowedToEdit($user))
			App::abort(401, 'Refused');
		
		$response = $this->usersAPIController->putSettings($user);

		// Handle error
		if ($response->getStatusCode() == 400) {
			$json = json_decode($response->getContent());
			
			// If the color was wrong
			if ($json->status == 'wrong_color')
				return Redirect::back()->with('warning', Lang::get('users.colorNotAllowed'));
		}

		if ($response->getStatusCode() == 200)
			return Redirect::back()->with('success', Lang::get('users.updateSettingsSuccessfull', ['login' => $user->login]));
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

		// We will use a custom message for the delete confirmation input
		$messages = [
    		'delete-confirmation.in' => Lang::get('users.writeDeleteHere'),
		];

		$validator = Validator::make($data, User::$rulesDestroy, $messages);

		if ($validator->fails())
			return Redirect::to(URL::route('users.edit', Auth::user()->login)."#delete-account")->withErrors($validator)->withInput(Input::except('password'));
		else {
			unset($data['delete-confirmation']);
			if ( ! Auth::validate($data))
				return Redirect::to(URL::route('users.edit', Auth::user()->login)."#delete-account")->withErrors(['password' => Lang::get('auth.passwordInvalid')])->withInput(Input::except('password'));
		}

		// Delete the user
		$this->usersAPIController->destroy();

		return Redirect::home()->with('success', Lang::get('users.deleteAccountSuccessfull'));
	}
}