<?php

class UsersAPIv1Controller extends BaseController {
	
	public function postUsers()
	{
		$data = [
			'login'    => Input::get('login'),
			'password' => Input::get('password'),
			'email'    => Input::get('email'),
		];

		// Validate login
		$validatorLogin = Validator::make(['login' => $data['login']], ['login' => User::$rulesSignup['login']]);
		if ($validatorLogin->fails()) {
			$data = [
				'status' => 'wrong_login',
				'error'  => $validatorLogin->messages()->first('login'),
			];

			return Response::json($data, 400);
		}

		// Validate password
		$validatorPassword = Validator::make(['password' => $data['password']], ['password' => User::$rulesSignup['password']]);
		if ($validatorPassword->fails()) {
			$data = [
				'status' => 'wrong_password',
				'error'  => $validatorPassword->messages()->first('password'),
			];

			return Response::json($data, 400);
		}

		// Validate email
		$validatorEmail = Validator::make(['email' => $data['email']], ['email' => User::$rulesSignup['email']]);
		if ($validatorEmail->fails()) {
			$data = [
				'status' => 'wrong_email',
				'error'  => $validatorEmail->messages()->first('email'),
			];

			return Response::json($data, 400);
		}

		// Store the new user
		$user             = new User;
		$user->login      = $data['login'];
		$user->email      = $data['email'];
		$user->password   = Hash::make($data['password']);
		$user->ip         = $_SERVER['REMOTE_ADDR'];
		$user->last_visit = Carbon::now()->toDateTimeString();
		$user->save();

		// Subscribe the user to the weekly newsletter
		Newsletter::createNewsletterForUser($user, 'weekly');

		// Send the welcome email
		Mail::send('emails.welcome', $data, function($m) use($data)
		{
			$m->to($data['email'], $data['login'])->subject(Lang::get('auth.subjectWelcomeEmail'));
		});

		return Response::json($user, 200);
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

		return Response::json($data);
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

		return Response::json($data, 200);
	}
}