<?php namespace TeenQuotes\Auth\Controllers;

use Auth, BaseController, Carbon, Input, Lang, Redirect, Request, Session, View;
use Laracasts\Validation\FormValidationException;
// use Laravel\Socialite\Contracts\Factory as Socialite;
use TeenQuotes\Users\Models\User;
use TeenQuotes\Users\Validation\UserValidator;

class AuthController extends BaseController {

	/**
	 * @var TeenQuotes\Users\Validation\UserValidator
	 */
	private $userValidator;

	/**
	 * @var Laravel\Socialite\Contracts\Factory
	 */
	private $socialite;

	public function __construct(UserValidator $userValidator)
	{
		$this->beforeFilter('guest', ['only' => 'getSignin']);
		$this->beforeFilter('auth', ['only' => 'getLogout']);

		$this->userValidator = $userValidator;
		// $this->socialite = $socialite;
	}

	/**
	 * Displays the signin form
	 *
	 * @return Response
	 */
	public function getSignin()
	{
		$data = [
			'pageTitle'               => Lang::get('auth.signinPageTitle'),
			'pageDescription'         => Lang::get('auth.signinPageDescription'),
			'requireLoggedInAddQuote' => Session::has('requireLoggedInAddQuote'),
		];

		return View::make('auth.signin', $data);
	}

	/**
	 * Handles the signin form submission
	 *
	 * @return Response
	 */
	public function postSignin()
	{
		$data = Input::only(['login', 'password']);

		try {
			$this->userValidator->validateSignin($data);
		}
		catch (FormValidationException $e)
		{
			return Redirect::route('signin')
				->withErrors($e->getErrors())
				->withInput(Input::except('password'));
		}

		// Try to log the user in.
		if (Auth::attempt($data, true)) {
			$user = Auth::user();
			$user->last_visit = Carbon::now()->toDateTimeString();
			$user->save();

			return Redirect::intended('/')->with('success', Lang::get('auth.loginSuccessfull', ['login' => $data['login']]));
		}
		// Maybe the user uses the old hash method
		else {
			$user = User::whereLogin($data['login'])->first();

			if (!is_null($user) AND ($user->password == User::oldHashMethod($data))) {
				// Update the password in database
				$user->password   = $data['password'];
				$user->last_visit = Carbon::now()->toDateTimeString();
				$user->save();

				Auth::login($user, true);

				return Redirect::intended('/')->with('success', Lang::get('auth.loginSuccessfull', ['login' => $data['login']]));
			}

			return Redirect::route('signin')->withErrors(array('password' => Lang::get('auth.passwordInvalid')))->withInput(Input::except('password'));
		}
	}

	/**
	 * Log a user out
	 *
	 * @return Response
	 */
	public function getLogout()
	{
		$login = Auth::user()->login;
		Auth::logout();

		return Redirect::route('home')->with('success', Lang::get('auth.logoutSuccessfull', compact('login')));
	}

	public function getAuthTwitter()
	{
		$hasCode = Request::has('code');

		if (! $hasCode) return $this->getTwitterAuthorization();

		$user = $this->getTwitterUser();
		// Prefill the signup view
		Session::flash('login', $user->getNickname());
		Session::flash('email', $user->getEmail());
		// Store the URL of the avatar is session
		// It'll be saved when calling the API
		Session::set('avatar', $user->getAvatar());

		return Redirect::route('signup');
	}

	/**
	* @return \Symfony\Component\HttpFoundation\RedirectResponse
	*/
	private function getTwitterAuthorization()
	{
		return $this->socialite->driver('twitter')->redirect();
	}
	/**
	* @return \Laravel\Socialite\Contracts\User
	*/
	private function getTwitterUser()
	{
		return $this->socialite->driver('twitter')->user();
	}
}