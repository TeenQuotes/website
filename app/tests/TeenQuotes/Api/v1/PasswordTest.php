<?php

use Laracasts\TestDummy\Factory;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Password;

class PasswordTest extends ApiTest {

	public function setUp()
	{
		parent::setUp();
		
		Factory::times($this->nbRessources)->create('User');

		$this->controller = App::make('TeenQuotes\Api\V1\Controllers\PasswordController');
	}

	public function testRemindNotFound()
	{
		$this->addInputReplace(['email' => 'foo']);

		$this->doRequest('postRemind');
		$this->assertStatusCodeIs(Response::HTTP_BAD_REQUEST)
			->withStatusMessage('wrong_user')
			->withErrorMessage("The email address doesn't match a user.");
	}

	public function testRemindExistingUser()
	{
		$user = User::find(1);

		$this->addInputReplace(['email' => $user->email]);

		$this->doRequest('postRemind');
		$this->assertStatusCodeIs(Response::HTTP_OK)
			->withStatusMessage('reminder_sent')
			->withSuccessMessage('An email was sent to the user.');
	}

	public function testResetErrors()
	{
		$this->performInvalidReset(Password::INVALID_PASSWORD, 'wrong_password', "The password is wrong.");
		$this->performInvalidReset(Password::INVALID_TOKEN, 'wrong_token', "The reset token is invalid.");
		$this->performInvalidReset(Password::INVALID_USER, 'wrong_user', "The email address doesn't match a user.");
	}

	public function testResetSuccess()
	{
		Password::shouldReceive('reset')->once()->andReturn(Password::PASSWORD_RESET);
		
		$this->doRequest('postReset');
		
		$this->assertStatusCodeIs(Response::HTTP_OK)
			->withStatusMessage('password_reset')
			->withSuccessMessage("The new password has been set.");
	}

	/**
	 * Perform an invalid request to reset a password
	 * @param  string $return The return of the Password::reset call
	 * @param  string $status The expected status message
	 * @param  string $error  The expected error message
	 */
	private function performInvalidReset($return, $status, $error)
	{
		Password::shouldReceive('reset')->once()->andReturn($return);
		
		$this->doRequest('postReset');
		
		$this->assertStatusCodeIs(Response::HTTP_BAD_REQUEST)
			->withStatusMessage($status)
			->withErrorMessage($error);
	}
}