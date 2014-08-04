<?php
namespace TeenQuotes\Api\V1\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Carbon\Carbon;
use LucaDegasperi\OAuth2Server\Facades\ResourceServerFacade as ResourceServer;
use Thomaswelton\LaravelGravatar\Facades\Gravatar;
use Buonzz\GeoIP\Laravel4\Facades\GeoIP;
use TeenQuotes\Mail\MailSwitcher;
use \Country;
use \Newsletter;
use \Setting;
use \User;

class UsersController extends APIGlobalController {
	
	public function deleteUsers()
	{
		User::find(ResourceServer::getOwnerId())->delete();
		
		$data = [
			'status'  => 'user_deleted',
			'message' => 'The user has been deleted.'
		];

		return Response::json($data, 200);
	}

	public function getUsers()
	{
		return $this->getSingleUser(ResourceServer::getOwnerId());
	}

	public function postUsers($doValidation = true)
	{
		$data = [
			'login'    => Input::get('login'),
			'password' => Input::get('password'),
			'email'    => Input::get('email'),
		];

		if ($doValidation) {

			// Validate login, password and email
			foreach (array_keys(User::$rulesSignup) as $value) {
				
				$validator = Validator::make([$value => $data[$value]], [$value => User::$rulesSignup[$value]]);
				if ($validator->fails()) {
					$data = [
						'status' => 'wrong_'.$value,
						'error'  => $validator->messages()->first($value),
					];

					return Response::json($data, 400);
				}
			}
		}

		// Store the new user
		$user             = new User;
		$user->login      = $data['login'];
		$user->email      = $data['email'];
		$user->password   = Hash::make($data['password']);
		$user->ip         = $_SERVER['REMOTE_ADDR'];
		$user->last_visit = Carbon::now()->toDateTimeString();
		// Try to detect city and country
		$user->country    = self::detectCountry();
		$user->city       = self::detectCity();

		// If the new user has got a Gravatar, set the avatar
		if (Gravatar::exists($data['email']))
			$user->avatar = Gravatar::src($data['email'], 150);

		$user->save();

		// Log the user in
		// The call was made from the UsersController
		if (!$doValidation)
			Auth::login($user);

		// Subscribe the user to the weekly newsletter
		Newsletter::createNewsletterForUser($user, 'weekly');

		// Send the welcome email via Postfix
		new MailSwitcher('sendmail');
		Mail::send('emails.welcome', $data, function($m) use($data)
		{
			$m->to($data['email'], $data['login'])->subject(Lang::get('auth.subjectWelcomeEmail', ['login' => $data['login']]));
		});

		return Response::json($user, 200, [], JSON_NUMERIC_CHECK);
	}

	public function getSingleUser($user_id)
	{
		$user = User::where('login', '=', $user_id)
		->orWhere('id', '=', $user_id)
		->with(array('countryObject' => function($q)
		{
			$q->addSelect(array('id', 'name'));
		}))
		->with(array('newsletters' => function($q)
		{
			$q->addSelect('user_id', 'type', 'created_at');
		}))
		->first();

		// User not found
		if (empty($user) OR $user->count() == 0) {
			$data = [
				'status' => 404,
				'error' => 'User not found.'
			];

			return Response::json($data, 404);
		}

		$data = $user->toArray();
		foreach (User::$appendsFull as $key) {
			$method = Str::camel('get_'.$key);
			$data[$key] = $user->$method();
		}

		return Response::json($data, 200, [], JSON_NUMERIC_CHECK);
	}

	public function getSearch($query)
	{
		$page = Input::get('page', 1);
		$pagesize = Input::get('pagesize', Config::get('app.quotes.nbQuotesPerPage'));

        if ($page <= 0)
			$page = 1;
				
		// Get users
		$content = self::getUsersSearch($page, $pagesize, $query);

		// Handle no users found
		$totalUsers = 0;
		if (is_null($content) OR empty($content) OR $content->count() == 0) {
			$data = [
				'status' => 404,
				'error' => 'No users have been found.'
			];

			return Response::json($data, 404);
		}

		$totalUsers = User::partialLogin($query)->notHidden()->count();

		$data = self::paginateContent($page, $pagesize, $totalUsers, $content, 'users');
		
		return Response::json($data, 200, [], JSON_NUMERIC_CHECK);
	}

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

		if ($doValidation) {

			// Validate login, password and email
			foreach (array_keys(User::$rulesUpdate) as $value) {
				
				$validator = Validator::make([$value => $data[$value]], [$value => User::$rulesUpdate[$value]]);
				if ($validator->fails()) {
					$data = [
						'status' => 'wrong_'.$value,
						'error'  => $validator->messages()->first($value),
					];

					return Response::json($data, 400);
				}
			}
		}

		// Everything went fine, update the user
		$user = ResourceServer::getOwnerId() ? User::find(ResourceServer::getOwnerId()) : Auth::user();
		
		if (!empty($data['gender']))
			$user->gender    = $data['gender'];
		if (!empty($data['country']))
			$user->country   = $data['country'];
		if (!empty($data['city']))
			$user->city      = $data['city'];
		if (!empty($data['about_me']))
			$user->about_me  = $data['about_me'];
		$user->birthdate = empty($data['birthdate']) ? NULL : $data['birthdate'];

		// Move the avatar
		if (!is_null($data['avatar'])) {
			$filename = $user->id.'.'.$data['avatar']->getClientOriginalExtension();

			Input::file('avatar')->move(Config::get('app.users.avatarPath'), $filename);

			$user->avatar = $filename;
		}

		$user->save();

		$data = [
			'status'  => 'profile_updated',
			'success' => 'The profile has been updated.'
		];

		return Response::json($data, 200);
	}

	public function putPassword()
	{
		$user = User::find(ResourceServer::getOwnerId());

		$data = [
			'password'              => Input::get('password'),
			'password_confirmation' => Input::get('password_confirmation'),
		];

		$validatorPassword = Validator::make($data, User::$rulesUpdatePassword);

		// Validate password
		if ($validatorPassword->fails()) {
			$data = [
				'status' => 'wrong_password',
				'error'  => $validatorPassword->messages()->first('password'),
			];

			return Response::json($data, 400);
		}

		// Update new password
		$user->password = Hash::make($data['password']);
		$user->save();

		$data = [
			'status'  => 'password_updated',
			'success' => 'The new password has been set.',
		];

		return Response::json($data, 200, [], JSON_NUMERIC_CHECK);
	}

	public function putSettings($userInstance = null)
	{
		if (is_null($userInstance))
			$user = User::find(ResourceServer::getOwnerId());
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

		$user->notification_comment_quote = $data['notification_comment_quote'];
		$user->hide_profile               = $data['hide_profile'];
		$user->save();

		// Update daily / weekly newsletters
		foreach (['daily', 'weekly'] as $newsletterType)
		{
			// The user wants the newsletter
			if ($data[$newsletterType.'_newsletter']) {
				// He was NOT already subscribed, store this in storage
				if (!$user->isSubscribedToNewsletter($newsletterType))
					Newsletter::createNewsletterForUser($user, $newsletterType);

				// He was already subscribed, do nothing
			}
			// The user doesn't want the newsletter
			else {
				// He was subscribed, delete this from storage
				if ($user->isSubscribedToNewsletter($newsletterType))
					Newsletter::forUser($user)->type($newsletterType)->delete();

				// He was not subscribed, do nothing
			}
		}

		// Update colors for quotes
		if (!in_array($data['colors'], Config::get('app.users.colorsAvailableQuotesPublished'))) {

			$data = [
				'status' => 'wrong_color',
				'error'  => 'This color is not allowed.'
			];

			return Response::json($data, 400);
		}

		// Forget value in cache
		Cache::forget(User::$cacheNameForColorsQuotesPublished.$user->id);

		// Retrieve setting by the attributes
		// or instantiate a new instance
		$colorSetting = Setting::firstOrNew(
		[
			'user_id' => $user->id,
			'key'     => 'colorsQuotesPublished'
		]);
		$colorSetting->value = $data['colors'];
		$colorSetting->save();

		$data = [
			'status'  => 'profile_updated',
			'success' => 'The profile has been updated.'
		];

		return Response::json($data, 200);
	}

	public static function getUsersSearch($page, $pagesize, $query)
	{
		// Number of users to skip
        $skip = $pagesize * ($page - 1);

        $users = User::partialLogin($query)
        ->notHidden()
        ->with('countryObject')
        ->skip($skip)
        ->take($pagesize)
        ->get();

        return $users;
	}

	/**
	 * Try to detect the country of the user, otherwise select the default country (the most common one)
	 * @return string The country
	 */
	public static function detectCountry()
	{
		// List of know countries
		$availableCountries = Country::lists('name', 'id');

		try {
			$countryDetected = GeoIP::getCountry();
		} catch (\Exception $e) {
			$selectedCountry = Country::getDefaultCountry();
		}

		// If the detected country in the possible countries, we will select it
		if (!isset($selectedCountry) AND in_array($countryDetected, array_values($availableCountries)))
			$selectedCountry = array_search($countryDetected, $availableCountries);
		else
			$selectedCountry = Country::getDefaultCountry();

		return $selectedCountry;
	}

	/**
	 * Try to detect the city of the user thanks to its IP address
	 * @return string The city detected
	 */
	public static function detectCity()
	{
		try {
			$cityDetected = GeoIP::getCity();
			return $cityDetected;
		} catch (\Exception $e) {
			$selectedCity = "";
			return $selectedCity;
		}
	}
}