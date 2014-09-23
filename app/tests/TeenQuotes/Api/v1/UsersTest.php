<?php

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Laracasts\TestDummy\Factory;

class UsersTest extends ApiTest {

	protected $requiredAttributes = ['id', 'login', 'email', 'last_visit', 'created_at', 'profile_hidden', 'url_avatar', 'wants_notification_comment_quote', 'is_admin'];
	protected $requiredFull = ['id', 'login', 'email', 'birthdate', 'gender', 'country', 'city', 'about_me', 'last_visit', 'created_at', 'profile_hidden', 'url_avatar', 'wants_notification_comment_quote', 'is_admin', 'total_comments', 'favorite_count', 'added_fav_count', 'published_quotes_count', 'is_subscribed_to_daily', 'is_subscribed_to_weekly'];
	protected $contentType = 'users';
	protected $embedsRelation = ['newsletters', 'country'];

	public function setUp()
	{
		parent::setUp();

		$this->controller = App::make('TeenQuotes\Api\V1\Controllers\UsersController');

		Factory::times($this->nbRessources)->create('User');
		
		// Attach a country for each user
		for ($i = 1; $i <= $this->nbRessources; $i++) { 
			$c = Factory::create('Country');
			$u = User::find($i);
			$u->country = $c['id'];
			$u->save();
		}
	}

	public function testStoreSmallLogin()
	{
		$this->addInputReplace([
			'login'    => 'a',
			'email'    => 'bob@example.com',
			'password' => 'azerty'
		]);

		$this->assertStoreError('wrong_login', 'The login must be at least 3 characters.');
	}

	public function testStoreAlreadyTakenLogin()
	{
		$u = User::find(1);

		$this->addInputReplace([
			'login'    => $u['login'],
			'email'    => 'bob@example.com',
			'password' => 'azerty'
		]);

		$this->assertStoreError('wrong_login', 'The login has already been taken.');
	}

	public function testStoreWrongLoginFormat()
	{
		$this->addInputReplace([
			'login'    => '!$$$',
			'email'    => 'bob@example.com',
			'password' => 'azerty'
		]);

		$this->assertStoreError('wrong_login', 'The login may only contain letters, numbers, and dashes.');
	}

	public function testStoreLongLogin()
	{
		$this->addInputReplace([
			'login'    => $this->generateString(21),
			'email'    => 'bob@example.com',
			'password' => 'azerty'
		]);

		$this->assertStoreError('wrong_login', 'The login may not be greater than 20 characters.');
	}

	public function testStoreWrongPassword()
	{
		$this->addInputReplace([
			'login'    => $this->generateString(10),
			'email'    => 'bob@example.com',
			'password' => 'azert'
		]);

		$this->assertStoreError('wrong_password', 'The password must be at least 6 characters.');
	}

	public function testStoreWrongEmail()
	{
		$this->addInputReplace([
			'login'    => $this->generateString(10),
			'email'    => 'bob',
			'password' => 'azerty'
		]);

		$this->assertStoreError('wrong_email', 'The email must be a valid email address.');
	}

	public function testStoreAlreadyTakenEmail()
	{
		$u = User::find(1);

		$this->addInputReplace([
			'login'    => $this->generateString(10),
			'email'    => $u['email'],
			'password' => 'azerty'
		]);

		$this->assertStoreError('wrong_email', 'The email has already been taken.');
	}

	public function testStoreSuccess()
	{
		$this->addInputReplace([
			'login'    => $this->generateString(10),
			'email'    => 'bob@example.com',
			'password' => 'azerty'
		]);

		// Set a fake IP
		$_SERVER['REMOTE_ADDR'] = '22.22.22.22';

		$this->tryStore()
			->assertStatusCodeIs(Response::HTTP_CREATED)
			->assertResponseHasRequiredAttributes();

		// Check that the user has been subscribed to the weekly newsletter
		$u = User::find($this->json->id);
		$this->assertTrue($u->getIsSubscribedToWeekly());
		$this->assertFalse($u->getIsSubscribedToDaily());

		// TODO: assert that the welcome e-mail was sent
	}

	public function testDeleteSuccess()
	{
		$idUser = 1;
		$this->logUserWithId($idUser);

		$this->doRequest('destroy')
			->assertStatusCodeIs(Response::HTTP_OK)
			->withStatusMessage('user_deleted')
			->withSuccessMessage('The user has been deleted.');

		$this->assertEmpty(User::find($idUser));
	}

	public function testGetInfoLoggedInUser()
	{
		$idUser = 1;
		$this->logUserWithId($idUser);

		$this->setFullAttributesAreRequired();

		$this->doRequest('getUsers')
			->assertStatusCodeIs(Response::HTTP_OK)
			->assertResponseMatchesExpectedSchema();

		$this->assertResponseKeyIs('id', $idUser);
	}

	public function testUserShowNotFound()
	{
		$this->tryShowNotFound()
			->withStatusMessage(404)
			->withErrorMessage('User not found.');
	}

	public function testShowFound()
	{
		$this->setFullAttributesAreRequired();

		for ($i = 1; $i <= $this->nbRessources ; $i++)
			$this->tryShowFound($i);
	}

	private function assertStoreError($status, $error)
	{
		$this->tryStore()
			->assertStatusCodeIs(Response::HTTP_BAD_REQUEST)
			->withStatusMessage($status)
			->withErrorMessage($error);
	}

	private function setFullAttributesAreRequired()
	{
		$this->requiredAttributes = $this->requiredFull;
	}
}