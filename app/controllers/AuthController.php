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
		if ($validator->passes())
		{
			// Try to log the user in.
			if (Auth::attempt($data))
				return Redirect::to('')->with('success', Lang::get('auth.logginSuccessfull'));
			else
				return Redirect::route('signin')->withErrors(array('password' => Lang::get('auth.passwordInvalid')))->withInput(Input::except('password'));
		}

		// Something went wrong.
		return Redirect::route('signin')->withErrors($validator)->withInput(Input::except('password'));
	}
}