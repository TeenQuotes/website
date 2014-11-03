<?php namespace TeenQuotes\Auth\Controllers;

use BaseController;
use Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Laracasts\Validation\FormValidationException;
use TeenQuotes\Users\Models\User;
use TeenQuotes\Users\Validation\UserValidator;

class AuthController extends BaseController {

	/**
	 * @var TeenQuotes\Users\Validation\UserValidator
	 */
	private $userValidator;

	public function __construct(UserValidator $userValidator)
	{
		$this->beforeFilter('guest', ['only' => 'getSignin']);
		$this->beforeFilter('auth', ['only' => 'getLogout']);

		$this->userValidator = $userValidator;
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
			return Redirect::route('signin')->withErrors($validator)->withInput(Input::except('password'));
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
}