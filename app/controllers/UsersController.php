<?php

class UsersController extends \BaseController {

	public function __construct()
    {
        $this->beforeFilter('guest', array('on' => 'store'));
    }

    /**
	 * Displays the signup form
	 *
	 * @return Response
	 */
	public function getSignup()
    {
    	Return View::make('auth.signup');
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
			
			// Store the user
			$user = new User;
			$user->login = $data['login'];
			$user->email = $data['email'];
			$user->password = Hash::make($data['password']);
			$user->ip = $_SERVER['REMOTE_ADDR'];
			$user->last_visit = Carbon::now()->toDateTimeString();
			$user->save();

			// Log the user
			Auth::login($user);

			// TODO : send an email
			
			return Redirect::intended('/')->with('success', Lang::get('auth.signupSuccessfull', array('login' => $data['login'])));
		}

		// Something went wrong.
		return Redirect::route('signup')->withErrors($validator)->withInput(Input::except('password'));
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$user = User::where('login', $id)->orWhere('id', $id)->first();

		return $user->login;
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

}