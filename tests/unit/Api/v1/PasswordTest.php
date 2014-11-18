<?php

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Password;
use TeenQuotes\Users\Models\User;

class PasswordTest extends ApiTest {

	protected $requiredAttributes = [];

	protected function _before()
	{
		parent::_before();
		
		$this->unitTester->insertInDatabase($this->unitTester->getNbRessources(), 'User');

		$this->unitTester->setController(App::make('TeenQuotes\Api\V1\Controllers\PasswordController'));
	}

	public function testRemindNotFound()
	{
		$this->unitTester->addInputReplace(['email' => 'foo']);

		$this->unitTester->doRequest('postRemind');
		$this->unitTester->assertStatusCodeIs(Response::HTTP_BAD_REQUEST)
			->withStatusMessage('wrong_user')
			->withErrorMessage("The email address doesn't match a user.");
	}

	public function testRemindExistingUser()
	{
		$user = User::find(1);

		$this->unitTester->addInputReplace(['email' => $user->email]);

		$this->unitTester->doRequest('postRemind');
		$this->unitTester->assertStatusCodeIs(Response::HTTP_OK)
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
		
		$this->unitTester->doRequest('postReset');
		
		$this->unitTester->assertStatusCodeIs(Response::HTTP_OK)
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
		
		$this->unitTester->doRequest('postReset');
		
		$this->unitTester->assertStatusCodeIs(Response::HTTP_BAD_REQUEST)
			->withStatusMessage($status)
			->withErrorMessage($error);
	}
}