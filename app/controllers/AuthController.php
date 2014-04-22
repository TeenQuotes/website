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

		return View::make('auth.signin');
	}

	/**
	 * Handles the signin form submission
	 *
	 * @return Response
	 */
	public function postSignin()
	{
		$data = [
			'login' => Input::get('login'),
			'password' => Input::get('password'),
		];

		$validator = Validator::make($data, User::$rulesSignin);

		// Check if the form validates with success.
		if ($validator->passes()) {
			// Try to log the user in.
			if (Auth::attempt($data, true))
				return Redirect::intended('/')->with('success', Lang::get('auth.logginSuccessfull', array('login' => $data['login'])));
			else
				return Redirect::route('signin')->withErrors(array('password' => Lang::get('auth.passwordInvalid')))->withInput(Input::except('password'));
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