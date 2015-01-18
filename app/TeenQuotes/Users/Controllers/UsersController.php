<?php namespace TeenQuotes\Users\Controllers;

use App, Auth, BaseController, Carbon, Config, Input, Lang, Paginator;
use Redirect, Response, Session, URL, View;
use Illuminate\Database\Eloquent\Collection;
use Laracasts\Validation\FormValidationException;
use TeenQuotes\Api\V1\Controllers\UsersController as UsersAPIController;
use TeenQuotes\Comments\Repositories\CommentRepository;
use TeenQuotes\Countries\Repositories\CountryRepository;
use TeenQuotes\Exceptions\HiddenProfileException;
use TeenQuotes\Exceptions\UserNotFoundException;
use TeenQuotes\Quotes\Repositories\FavoriteQuoteRepository;
use TeenQuotes\Quotes\Repositories\QuoteRepository;
use TeenQuotes\Settings\Repositories\SettingRepository;
use TeenQuotes\Users\Models\User;
use TeenQuotes\Users\Repositories\UserRepository;
use TeenQuotes\Users\Validation\UserValidator;

class UsersController extends BaseController {

	/**
	 * @var TeenQuotes\Api\V1\Controllers\UsersController
	 */
	private $api;

	/**
	 * @var TeenQuotes\Comments\Repositories\CommentRepository
	 */
	private $commentRepo;

	/**
	 * @var TeenQuotes\Countries\Repositories\CountryRepository
	 */
	private $countryRepo;

	/**
	 * @var TeenQuotes\Quotes\Repositories\FavoriteQuoteRepository
	 */
	private $favQuoteRepo;

	/**
	 * @var TeenQuotes\Quotes\Repositories\QuoteRepository
	 */
	private $quoteRepo;

	/**
	 * @var TeenQuotes\Settings\Repositories\SettingRepository
	 */
	private $settingRepo;

	/**
	 * @var TeenQuotes\Users\Repositories\UserRepository
	 */
	private $userRepo;

	/**
	 * @var TeenQuotes\Users\Validation\UserValidator
	 */
	private $userValidator;

	public function __construct(CommentRepository $commentRepo, CountryRepository $countryRepo,
		FavoriteQuoteRepository $favQuoteRepo, QuoteRepository $quoteRepo,
		SettingRepository $settingRepo, UserRepository $userRepo, UserValidator $userValidator)
	{
		$this->beforeFilter('guest', ['only' => 'store']);
		$this->beforeFilter('auth', ['only' => ['edit', 'update', 'putPassword', 'putSettings']]);

		$this->api           = App::make('TeenQuotes\Api\V1\Controllers\UsersController');
		$this->commentRepo   = $commentRepo;
		$this->countryRepo   = $countryRepo;
		$this->favQuoteRepo  = $favQuoteRepo;
		$this->quoteRepo     = $quoteRepo;
		$this->settingRepo   = $settingRepo;
		$this->userRepo      = $userRepo;
		$this->userValidator = $userValidator;
	}

	public function redirectOldUrl($userId)
	{
		$user = $this->userRepo->getById($userId);

		return Redirect::route('users.show', $user->login, 301);
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
		$data = Input::only(['login']);

		try {
			$this->userValidator->validateLogin($data);
		}
		catch (FormValidationException $e)
		{
			return Response::json([
				'success' => false,
				'message' => $e->getErrors()->first('login'),
				'failed'  => $this->userValidator->getFailedReasonFor('login'),
			]);
		}

		return Response::json([
			'success' => true,
			'message' => $data['login'].'? '.Lang::get('auth.loginAwesome'),
		]);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$data = Input::only(['login', 'password', 'email']);

		// Check if the form validates with success.
		$this->userValidator->validateSignup($data);

		// Call the API - skip the API validator
		$response = $this->api->store(false);
		if ($response->getStatusCode() == 201)
		{
			// Log the user in
			Auth::login($response->getOriginalData());

			if (Session::has('url.intended'))
				return Redirect::intended('/')->with('success', Lang::get('auth.signupSuccessfull', ['login' => $data['login']]));

			return Redirect::route('users.show', $data['login'])->with('success', Lang::get('auth.signupSuccessfull', ['login' => $data['login']]));
		}
	}

	/**
	 * Redirect the user to a place where we have content to show if possible
	 * @param  TeenQuotes\Users\Models\User $user The user
	 * @param  string $type The requested type to show
	 * @return Response|null If null is returned, we can't find a better place to show content
	 */
	private function redirectUserIfContentNotAvailable($user, $type)
	{
		// Check where we can redirect the user
		$publishPossible   = $user->hasPublishedQuotes();
		$favoritesPossible = $user->hasFavoriteQuotes();
		$commentsPossible  = $user->hasPostedComments();

		// Check if we have content to display
		// If we have nothing to show, try to redirect somewhere else
		switch ($type) {
			case 'favorites':
				if ( ! $favoritesPossible) {
					if ($publishPossible)
						return Redirect::route('users.show', $user->login);
					if ($commentsPossible)
						return Redirect::route('users.show', [$user->login, 'comments']);
				}
				break;

			case 'comments':
				if ( ! $commentsPossible) {
					if ($publishPossible)
						return Redirect::route('users.show', $user->login);
					if ($favoritesPossible)
						return Redirect::route('users.show', [$user->login, 'favorites']);
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

		return null;
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
		$user = $this->userRepo->getByLogin($user_id);

		if (is_null($user)) throw new UserNotFoundException;

		// Register the view in the recommendation system
		$user->registerViewUserProfile();

		// Try to redirect to a better place if content is available
		$redirect = $this->redirectUserIfContentNotAvailable($user, $type);
		if ( ! is_null($redirect))
			return $redirect;

		// Throw an exception if the user has an hidden profile
		// We do not throw this exception if the user is currently
		// viewing its own hidden profile
		if ($user->isHiddenProfile() AND ! $this->userViewingSelfProfile($user))
			throw new HiddenProfileException;

		// Build the data array. Keys: quotes, paginator
		$methodName = 'dataShow'.ucfirst($type);
		$data = $this->$methodName($user);

		$data['user']               = $user;
		// Used for deep linking in ProfileComposer
		$data['type']               = $type;
		$data['viewingSelfProfile'] = $this->userViewingSelfProfile($user);
		$data['pageTitle']          = Lang::get('users.profilePageTitle', ['login' => $user->login]);
		$data['pageDescription']    = Lang::get('users.profilePageDescription', ['login' => $user->login]);

		// If the user is new and is viewing its own profile, a small welcome tutorial
		if ($this->shouldDisplayWelcomeTutorial($data['quotes'], $user))
			return View::make('users.welcome', $data);

		return View::make('users.show', $data);
	}

	private function shouldDisplayWelcomeTutorial($quotes, $user)
	{
		return (
			(empty($quotes) OR (($quotes instanceof Collection) AND $quotes->isEmpty()))
			AND $this->userViewingSelfProfile($user)
		);
	}

	private function dataShowFavorites(User $user)
	{
		$pageNumber = Input::get('page', 1);

		// Get the list of favorite quotes
		$quotesFavorited = $this->favQuoteRepo->quotesFavoritesForUser($user);

		// Fetch the quotes
		$quotes = $this->quoteRepo->getForIds($quotesFavorited, $pageNumber, Config::get('app.users.nbQuotesPerPage'));

		// Build the associated paginator
		$paginator = Paginator::make($quotes->toArray(), count($quotesFavorited), Config::get('app.users.nbQuotesPerPage'));
		$paginator->setBaseUrl(URL::route('users.show', [$user->login, 'favorites'], false));

		return compact('quotes', 'paginator');
	}

	private function dataShowComments(User $user)
	{
		$page = Input::get('page', 1);

		$comments = $this->commentRepo->findForUser($user, $page, Config::get('app.users.nbQuotesPerPage'));

		// Build the associated paginator
		$paginator = Paginator::make($comments->toArray(), $user->getTotalComments(), Config::get('app.users.nbQuotesPerPage'));
		$paginator->setBaseUrl(URL::route('users.show', [$user->login, 'comments'], false));

		return [
			'quotes'    => $comments,
			'paginator' => $paginator,
		];
	}

	private function dataShowPublished(User $user)
	{
		$pageNumber = Input::get('page', 1);

		$quotes = $this->quoteRepo->getQuotesByApprovedForUser($user, 'published', $pageNumber, Config::get('app.users.nbQuotesPerPage'));

		$numberQuotesPublishedForUser = $user->getPublishedQuotesCount();

		// Build the associated paginator
		$paginator = Paginator::make($quotes->toArray(), $numberQuotesPublishedForUser, Config::get('app.users.nbQuotesPerPage'));

		return compact('quotes', 'paginator');
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  string $id The login of the user
	 * @throws TeenQuotes\Exceptions\UserNotFoundException
	 * @return Response
	 */
	public function edit($id)
	{
		$user = $this->userRepo->getByLogin($id);

		if (is_null($user) OR ! $this->userIsAllowedToEdit($user))
			throw new UserNotFoundException;

		// The color for published quotes
		$confColor = $this->settingRepo->findForUserAndKey($user, 'colorsQuotesPublished');

		// Set the default color
		if (is_null($confColor))
			$selectedColor = Config::get('app.users.defaultColorQuotesPublished');
		else
			$selectedColor = $confColor->value;

		list($selectedCountry, $selectedCity) = $this->getCountryAndCity($user);

		$data = [
			'gender'           => $user->gender,
			'listCountries'    => $this->countryRepo->listNameAndId(),
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
	 * @param  TeenQuotes\Users\Models\User $user The user model
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

		$this->userValidator->validateUpdateProfile($data);

		// Call the API
		$response = $this->api->putProfile(false);
		if ($response->getStatusCode() == 200)
			return Redirect::back()->with('success', Lang::get('users.updateProfileSuccessfull', ['login' => Auth::user()->login]));

		App::abort(500, "Can't update profile");
	}

	/**
	 * Update the password in storage
	 *
	 * @param  string $id The login of the user
	 * @return Response
	 */
	public function putPassword($id)
	{
		$data = Input::only(['password', 'password_confirmation']);

		try {
			$this->userValidator->validateUpdatePassword($data);
		}
		catch (FormValidationException $e)
		{
			return Redirect::to(URL::route('users.edit', Auth::user()->login)."#edit-password")
				->withErrors($e->getErrors())
				->withInput(Input::all());
		}

		$user = $this->userRepo->getByLogin($id);
		if (! $this->userIsAllowedToEdit($user))
			App::abort(401, 'Refused');

		$this->userRepo->updatePassword($user, $data['password']);

		return Redirect::back()->with('success', Lang::get('users.updatePasswordSuccessfull', ['login' => $user->login]));
	}

	/**
	 * Update settings for the user
	 *
	 * @param  string $id The login of the user
	 * @return Response
	 */
	public function putSettings($id)
	{
		$user = $this->userRepo->getByLogin($id);
		if ( ! $this->userIsAllowedToEdit($user))
			App::abort(401, 'Refused');

		$response = $this->api->putSettings($user);

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

		try {
			$this->userValidator->validateDestroy($data, $messages);
		}
		catch (FormValidationException $e)
		{
			return $this->redirectToDeleteAccount(Auth::user()->login)
				->withErrors($e->getErrors())
				->withInput(Input::except('password'));
		}

		unset($data['delete-confirmation']);
		if ( ! Auth::validate($data))
			return $this->redirectToDeleteAccount(Auth::user()->login)
				->withErrors(['password' => Lang::get('auth.passwordInvalid')])
				->withInput(Input::except('password'));

		// Delete the user
		$this->api->destroy();

		return Redirect::home()->with('success', Lang::get('users.deleteAccountSuccessfull'));
	}

	private function redirectToDeleteAccount($login)
	{
		return Redirect::to(URL::route('users.edit', $login)."#delete-account");
	}
}