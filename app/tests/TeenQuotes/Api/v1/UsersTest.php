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

	public function testPutPasswordTooSmall()
	{
		$this->addInputReplace([
			'password'              => 'azert',
			'password_confirmation' => 'azert',
		]);

		$this->assertPutPasswordError('wrong_password', 'The password must be at least 6 characters.');
	}

	public function testPutPasswordNotSame()
	{
		$this->addInputReplace([
			'password'              => 'azerty',
			'password_confirmation' => 'azert',
		]);

		$this->assertPutPasswordError('wrong_password', 'The password confirmation does not match.');
	}

	public function testPutPasswordSuccess()
	{
		$newPassword = 'azerty';
		$idUser = 1;

		$this->addInputReplace([
			'password'              => $newPassword,
			'password_confirmation' => $newPassword,
		]);

		$u = $this->logUserWithId($idUser);

		$this->doRequest('putPassword')
			->assertStatusCodeIs(Response::HTTP_OK)
			->withStatusMessage('password_updated')
			->withSuccessMessage('The new password has been set.');

		// Check that the new password has been set
		$this->assertTrue(Auth::attempt(['login' => $u['login'], 'password' => $newPassword]));
	}

	public function testPutProfileWrongGender()
	{
		$this->addInputReplace([
			'gender' => 'foo',
		]);

		$this->assertPutProfileError('wrong_gender', 'The selected gender is invalid.');
	}

	public function testPutProfileWrongBirthdate()
	{
		$this->addInputReplace([
			'gender'    => 'M',
			'birthdate' => '1975-01-32'
		]);

		$this->assertPutProfileError('wrong_birthdate', 'The birthdate does not match the format Y-m-d.');
	}

	public function testPutProfileWrongCountry()
	{
		$this->addInputReplace([
			'gender'    => 'M',
			'birthdate' => '1975-01-25',
			'country'   => Country::all()->count() + 1
		]);

		$this->assertPutProfileError('wrong_country', 'The selected country was not found.');
	}

	public function testPutProfileWrongAboutMe()
	{
		$this->addInputReplace([
			'gender'    => 'M',
			'birthdate' => '1975-01-25',
			'country'   => Country::first()->id,
			'about_me'  => $this->generateString(501)
		]);

		$this->assertPutProfileError('wrong_about_me', 'The about me may not be greater than 500 characters.');
	}

	// TODO: write tests for uploaded files

	public function testPutProfileSuccess()
	{
		$gender = 'M';
		$birthdate = '1975-01-25';
		$country = Country::first()->id;
		$about_me = $this->generateString(200);

		$data = compact('gender', 'birthdate', 'country', 'about_me');
		$this->addInputReplace($data);

		$this->logUserWithId(1);

		$this->doRequest('putProfile')
			->assertStatusCodeIs(Response::HTTP_OK)
			->withStatusMessage('profile_updated')
			->withSuccessMessage('The profile has been updated.');
	}

	public function testPutSettingsWrongColor()
	{
		$this->addInputReplace([
			'notification_comment_quote' => true,
			'hide_profile'               => true,
			'weekly_newsletter'          => true,
			'daily_newsletter'           => true,
			'colors'                     => 'foo'
		]);

		$this->logUserWithId(1);

		$this->doRequest('putSettings')
			->assertStatusCodeIs(Response::HTTP_BAD_REQUEST)
			->withStatusMessage('wrong_color')
			->withErrorMessage('This color is not allowed.');
	}

	public function testPutSettingsSuccess()
	{
		$u = $this->logUserWithId(1);

		// Check default values for a new profile
		$this->assertTrue($u->getIsSubscribedToWeekly());
		$this->assertFalse($u->getIsSubscribedToDaily());
		$this->assertFalse($u->isHiddenProfile());
		$this->assertTrue($u->wantsEmailComment());
		$this->assertEquals($u->getColorsQuotesPublished(), Config::get('app.users.defaultColorQuotesPublished'));

		// New color for quotes published
		$newColor = 'red';
		// Check that this is not the default value!
		$this->assertNotEquals(Config::get('app.users.defaultColorQuotesPublished'), $newColor);

		$this->addInputReplace([
			'notification_comment_quote' => false,
			'hide_profile'               => true,
			'weekly_newsletter'          => false,
			'daily_newsletter'           => true,
			'colors'                     => $newColor
		]);

		$this->doRequest('putSettings')
			->assertStatusCodeIs(Response::HTTP_OK)
			->withStatusMessage('profile_updated')
			->withSuccessMessage('The profile has been updated.');

		// Check that values have changed accordingly
		$this->assertFalse($u->getIsSubscribedToWeekly());
		$this->assertTrue($u->getIsSubscribedToDaily());
		$this->assertTrue($u->isHiddenProfile());
		$this->assertFalse($u->wantsEmailComment());
		
		// Verify cache for quotes published color
		$this->assertFalse(Cache::has(User::$cacheNameForColorsQuotesPublished.$u->id));
		$this->assertEquals($u->getColorsQuotesPublished(), $newColor);
		$this->assertEquals(Cache::get(User::$cacheNameForColorsQuotesPublished.$u->id), $newColor);
	}

	private function assertPutPasswordError($status, $error)
	{
		$this->logUserWithId(1);

		$this->doRequest('putPassword')
			->assertStatusCodeIs(Response::HTTP_BAD_REQUEST)
			->withStatusMessage($status)
			->withErrorMessage($error);
	}

	private function assertPutProfileError($status, $error)
	{
		$this->logUserWithId(1);

		$this->doRequest('putProfile')
			->assertStatusCodeIs(Response::HTTP_BAD_REQUEST)
			->withStatusMessage($status)
			->withErrorMessage($error);
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