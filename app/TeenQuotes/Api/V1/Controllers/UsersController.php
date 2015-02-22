<?php namespace TeenQuotes\Api\V1\Controllers;

use App, Auth, Config, DB, Exception, Input, Queue, Str;
use Carbon\Carbon;
use Laracasts\Validation\FormValidationException;
use stojg\crop\CropEntropy;
use TeenQuotes\Api\V1\Interfaces\PaginatedContentInterface;
use TeenQuotes\Countries\Localisation\Detector;
use TeenQuotes\Exceptions\ApiNotFoundException;
use TeenQuotes\Http\Facades\Response;
use TeenQuotes\Newsletters\Models\Newsletter;
use TeenQuotes\Users\Models\User;
use TeenQuotes\Users\Validation\UserValidator;
use Thomaswelton\LaravelGravatar\Facades\Gravatar;

class UsersController extends APIGlobalController implements PaginatedContentInterface {

	/**
	 * @var \TeenQuotes\Users\Validation\UserValidator
	 */
	private $userValidator;

	/**
	 * @var \TeenQuotes\Countries\Localisation\Detector
	 */
	private $localisationDetector;

	protected function bootstrap()
	{
		$this->userValidator        = App::make(UserValidator::class);
		$this->localisationDetector = App::make(Detector::class);
	}

	/**
	 * Destroy the account of the logged in user
	 * @return \TeenQuotes\Http\Facades\Response
	 */
	public function destroy()
	{
		$this->userRepo->destroy($this->retrieveUser());

		return Response::json([
			'status'  => 'user_deleted',
			'success' => 'The user has been deleted.'
		], 200);
	}


	/**
	 * Show information about the logged in user
	 * @return \TeenQuotes\Http\Facades\Response
	 */
	public function getUsers()
	{
		$u = $this->retrieveUser();

		return $this->show($u->id);
	}

	/**
	 * Create a new account
	 * @param  boolean $doValidation Do we need to perform validation?
	 * @return \TeenQuotes\Http\Facades\Response
	 */
	public function store($doValidation = true)
	{
		$data = Input::only(['login', 'password', 'email']);

		if ($doValidation)
			$this->userValidator->validateSignup($data);

		// Store the new user
		// If the new user has got a Gravatar, set the avatar
		$avatar = null;
		if (Gravatar::exists($data['email']))
			$avatar = Gravatar::src($data['email'], 150);

		// Try to detect the city and the country from the request
		$request = Input::instance();
		$country = $this->localisationDetector->detectCountry($request);
		$city = $this->localisationDetector->detectCity($request);

		$user = $this->userRepo->create($data['login'], $data['email'], $data['password'],
			$_SERVER['REMOTE_ADDR'], Carbon::now()->toDateTimeString(),
			$country, $city, $avatar
		);

		// Send a welcome e-mail and subscribe the user to the
		// weekly newsletter thanks to its observer

		return Response::json($user, 201, [], JSON_NUMERIC_CHECK);
	}

	/**
	 * Show a user's profile
	 * @param  int $user_id The ID of the user
	 * @return \TeenQuotes\Http\Facades\Response
	 */
	public function show($user_id)
	{
		$user = $this->userRepo->showByLoginOrId($user_id);

		// User not found
		if ($this->isNotFound($user))
			return Response::json([
				'status' => 404,
				'error' => 'User not found.'
			], 404);

		$data = $user->toArray();
		foreach (User::$appendsFull as $key)
		{
			$method = Str::camel('get_'.$key);
			$data[$key] = $user->$method();
		}

		$user->registerViewUserProfile();

		return Response::json($data, 200, [], JSON_NUMERIC_CHECK);
	}

	/**
	 * Search users without an hidden profile from a partial username
	 * @param  string $query
	 * @return \TeenQuotes\Http\Facades\Response
	 * @throws \TeenQuotes\Exceptions\ApiNotFoundException If no users were found
	 */
	public function getSearch($query)
	{
		// Get users
		$users = $this->getUsersSearch($this->getPage(), $this->getPagesize(), $query);

		// Count the total number of results
		$totalUsers = $this->userRepo->countByPartialLogin($query);

		return $this->buildPaginatedResponse($users, $totalUsers);
	}

	/**
	 * Update a user's profile
	 * @param  boolean $doValidation Do we perform validation?
	 * @return \TeenQuotes\Http\Facades\Response
	 */
	public function putProfile($doValidation = true)
	{
		$data = [
			'gender'    => Input::get('gender'),
			'birthdate' => Input::get('birthdate'),
			'country'   => Input::get('country'),
			'city'      => Input::get('city'),
			'about_me'  => Input::get('about_me'),
			'avatar'    => Input::file('avatar'),
		];

		if ($doValidation)
			$this->userValidator->validateUpdateProfile($data);

		// Everything went fine, update the user
		$user = $this->retrieveUser();

		$this->userRepo->updateProfile($user, $data['gender'], $data['country'],
			$data['city'], $data['about_me'], $data['birthdate'],
		$data['avatar']);

		// Move the avatar if required
		if ( ! is_null($data['avatar']))
			$this->cropAndMoveAvatar($user, $data['avatar']);

		return Response::json([
			'status'  => 'profile_updated',
			'success' => 'The profile has been updated.'
		], 200);
	}

	/**
	 * Update the password of the logged in user
	 * @return \TeenQuotes\Http\Facades\Response
	 */
	public function putPassword()
	{
		$user = $this->retrieveUser();

		$data = Input::only(['password', 'password_confirmation']);

		$this->userValidator->validateUpdatePassword($data);

		// Update the new password
		$this->userRepo->updatePassword($user, $data['password']);

		return Response::json([
			'status'  => 'password_updated',
			'success' => 'The new password has been set.',
		], 200, [], JSON_NUMERIC_CHECK);
	}

	/**
	 * Update the user's settings
	 * @param  \TeenQuotes\Users\Models\User|null $userInstance
	 * @return \TeenQuotes\Http\Facades\Response
	 */
	public function putSettings($userInstance = null)
	{
		if (is_null($userInstance))
			$user = $this->retrieveUser();
		else
			$user = $userInstance;

		// We just want booleans
		$data = [
			'notification_comment_quote' => Input::has('notification_comment_quote') ? filter_var(Input::get('notification_comment_quote'), FILTER_VALIDATE_BOOLEAN) : false,
			'hide_profile'               => Input::has('hide_profile') ? filter_var(Input::get('hide_profile'), FILTER_VALIDATE_BOOLEAN) : false,
			'weekly_newsletter'          => Input::has('weekly_newsletter') ? filter_var(Input::get('weekly_newsletter'), FILTER_VALIDATE_BOOLEAN) : false,
			'daily_newsletter'           => Input::has('daily_newsletter') ? filter_var(Input::get('daily_newsletter'), FILTER_VALIDATE_BOOLEAN) : false,
			'colors'                     => Input::get('colors'),
		];

		$this->userRepo->updateSettings($user, $data['notification_comment_quote'], $data['hide_profile']);

		// Update daily / weekly newsletters
		foreach (Newsletter::getPossibleTypes() as $newsletterType)
		{
			// The user wants the newsletter
			if ($data[$newsletterType.'_newsletter'])
			{
				// He was NOT already subscribed, store this in storage
				if ( ! $user->isSubscribedToNewsletter($newsletterType))
					$this->newslettersManager->createForUserAndType($user, $newsletterType);

				// He was already subscribed, do nothing
			}
			// The user doesn't want the newsletter
			else {
				// He was subscribed, delete this from storage
				if ($user->isSubscribedToNewsletter($newsletterType))
					$this->newslettersManager->deleteForUserAndType($user, $newsletterType);

				// He was not subscribed, do nothing
			}
		}

		// Update colors for quotes
		if ( ! in_array($data['colors'], Config::get('app.users.colorsAvailableQuotesPublished')))
			return $this->colorIsNotAllowed();

		$this->settingRepo->updateOrCreate($user, 'colorsQuotesPublished', $data['colors']);

		return Response::json([
			'status'  => 'profile_updated',
			'success' => 'The profile has been updated.'
		], 200);
	}

	/**
	 * Get users from a given country
	 * @param  int $country_id The ID of the country
	 * @return \TeenQuotes\Http\Facades\Response
	 * @throws \TeenQuotes\Exceptions\ApiNotFoundException If no users were found
	 */
	public function fromCountry($country_id)
	{
		$country = $this->countryRepo->findById($country_id);

		if ($this->isNotFound($country))
			return $this->countryWasNotFound($country_id);

		$users = $this->userRepo->fromCountry($country, $this->getPage(), $this->getPagesize());

		$totalUsers = $this->userRepo->countFromCountry($country);

		return $this->buildPaginatedResponse($users, $totalUsers);
	}

	/**
	 * Retrieve users from a partial username for a given page and pagesize
	 * @param  int $page
	 * @param  int $pagesize
	 * @param  string $query
	 * @return \Illuminate\Database\Eloquent\Collection
	 */
	public function getUsersSearch($page, $pagesize, $query)
	{
		return $this->userRepo->searchByPartialLogin($query, $page, $pagesize);
	}

	/**
	 * Get the pagesize
	 * @return int
	 */
	public function getPagesize()
	{
		return Input::get('pagesize', Config::get('app.quotes.nbQuotesPerPage'));
	}

	private function cropAndMoveAvatar(User $user, $avatar)
	{
		$filename = $user->id.'.'.$avatar->getClientOriginalExtension();
		$filepath = Config::get('app.users.avatarPath').'/'.$filename;

		// Save to the final location
		Input::file('avatar')->move(Config::get('app.users.avatarPath'), $filename);

		// Crop the image and save it
		$center = new CropEntropy($filepath);
		$croppedImage = $center->resizeAndCrop(Config::get('app.users.avatarWidth'), Config::get('app.users.avatarHeight'));
		$croppedImage->writeimage($filepath);
	}

	/**
	 * Tell that a country was not found
	 *
	 * @param  int $id The country's ID
	 * @return \TeenQuotes\Http\Facades\Response
	 */
	private function countryWasNotFound($id)
	{
		return Response::json([
			'status' => 'country_not_found',
			'error'  => "The country #".$id." was not found.",
		], 404);
	}

	/**
	 * Tell that a color is not allowed
	 * @return \TeenQuotes\Http\Facades\Response
	 */
	private function colorIsNotAllowed()
	{
		return Response::json([
			'status' => 'wrong_color',
			'error'  => 'This color is not allowed.'
		], 400);
	}

	/**
	 * Build a paginated response for users
	 *
	 * @param  \Illuminate\Database\Eloquent\Collection $users
	 * @param  int $total The total number of results for the ressource
	 * @throws \TeenQuotes\Exceptions\ApiNotFoundException If no users were found
	 * @return \TeenQuotes\Http\Facades\Response
	 */
	private function buildPaginatedResponse($users, $total)
	{
		// Handle no users found
		if ($this->isNotFound($users))
			throw new ApiNotFoundException('users');

		$data = self::paginateContent($this->getPage(), $this->getPagesize(), $total, $users, 'users');

		return Response::json($data, 200, [], JSON_NUMERIC_CHECK);
	}
}