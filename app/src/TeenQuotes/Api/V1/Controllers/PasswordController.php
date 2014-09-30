<?php namespace TeenQuotes\Api\V1\Controllers;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Response;
use Password;

class PasswordController extends APIGlobalController {
	
	public function postRemind()
	{
		$response = Password::remind(Input::only('email'), function($message)
		{
			$message->subject(Lang::get('auth.passwordReminderEmailSubject'));
		});

		switch ($response) {
			case Password::INVALID_USER:
				$status = 400;
				$data = [
					'status' => 'wrong_user',
					'error'  => "The email address doesn't match a user."
				];
				break;

			case Password::REMINDER_SENT:
				$status = 200;
				$data = [
					'status'  => 'reminder_sent',
					'success' => "An email was sent to the user."
				];
		}

		return Response::json($data, $status);
	}

	public function postReset()
	{
		// Here we don't use password_confirmation but we keep it
		// to call the reset function
		$credentials = [
			'email'                 => Input::get('email'),
			'token'                 => Input::get('token'),
			'password'              => Input::get('password'),
			'password_confirmation' => Input::get('password'),
		];

		$response = Password::reset($credentials, function($user, $password)
		{
			// Update the password in database
			$user->password = $password;
			$user->save();
		});

		switch ($response)
		{
			case Password::INVALID_PASSWORD:
				$status = 400;
				$data = [
					'status' => 'wrong_password',
					'error'  => 'The password is wrong.'
				];
				break;

			case Password::INVALID_TOKEN:
				$status = 400;
				$data = [
					'status' => 'wrong_token',
					'error'  => 'The reset token is invalid.'
				];
				break;

			case Password::INVALID_USER:
				$status = 400;
				$data = [
					'status' => 'wrong_user',
					'error'  => "The email address doesn't match a user."
				];
				break;


			case Password::PASSWORD_RESET:
				$status = 200;
				$data = [
					'status'  => 'password_reset',
					'success' => "The new password has been set."
				];
		}

		return Response::json($data, $status);
	}	
}