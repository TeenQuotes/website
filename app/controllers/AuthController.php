<?php

class AuthController extends \BaseController {

	/**
	 * Displays the signin form
	 *
	 * @return Response
	 */
	public function getSignin()
	{
		// Check if we are already logged in
		if (Auth::check()) {
			// Redirect to homepage
			return Redirect::route('home')->with('success', Lang::get('auth.alreadyLoggedIn'));
		}

		$data = [
			'pageTitle'       => Lang::get('auth.signinPageTitle'),
			'pageDescription' => Lang::get('auth.signinPageDescription'),
		];

		$data ['requireLoggedInAddQuote'] = Session::has('requireLoggedInAddQuote');

		if ($data['requireLoggedInAddQuote']) {
			// Send event to Google Analytics
			JavaScript::put([
				'eventCategory' => 'addquote',
				'eventAction'   => 'not-logged-in',
				'eventLabel'    => 'signin-page'
	    	]);
		}

		return View::make('auth.signin', $data);
	}

	/**
	 * Handles the signin form submission
	 *
	 * @return Response
	 */
	public function postSignin()
	{
		$data = [
			'login'    => Input::get('login'),
			'password' => Input::get('password'),
		];

		$validator = Validator::make($data, User::$rulesSignin);

		// Check if the form validates with success.
		if ($validator->passes()) {
			// Try to log the user in.
			if (Auth::attempt($data, true)) {
				$user = Auth::user();
				$user->last_visit = Carbon::now()->toDateTimeString();
				$user->save();

				return Redirect::intended('/')->with('success', Lang::get('auth.loginSuccessfull', array('login' => $data['login'])));
			}
			// Maybe the user uses the old hash method
			else {
				$user = User::whereLogin($data['login'])->first();

				if (!is_null($user) AND ($user->password == User::oldHashMethod($data))) {
					// Update the password in database
					$user->password   = Hash::make($data['password']);
					$user->last_visit = Carbon::now()->toDateTimeString();
					$user->save();

					Auth::login($user, true);

					return Redirect::intended('/')->with('success', Lang::get('auth.loginSuccessfull', array('login' => $data['login'])));
				}

				return Redirect::route('signin')->withErrors(array('password' => Lang::get('auth.passwordInvalid')))->withInput(Input::except('password'));
			}
		}

		// Something went wrong.
		return Redirect::route('signin')->withErrors($validator)->withInput(Input::except('password'));
	}

	public function getLogout()
	{
		if (Auth::check()) {
			$login = Auth::user()->login;
			Auth::logout();

			return Redirect::route('home')->with('success', Lang::get('auth.logoutSuccessfull', array('login' => $login)));
		}
		else
			return Redirect::route('home')->with('warning', Lang::get('auth.logoutNotLoggedIn'));
	}
}