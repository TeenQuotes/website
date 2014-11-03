<?php

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Laracasts\TestDummy\Factory;
use TeenQuotes\Countries\Models\Country;
use TeenQuotes\Users\Models\User;

class UsersTest extends ApiTest {

	protected $requiredAttributes = ['id', 'login', 'email', 'last_visit', 'created_at', 'profile_hidden', 'url_avatar', 'wants_notification_comment_quote', 'is_admin'];
	protected $requiredFull = ['id', 'login', 'email', 'birthdate', 'gender', 'country', 'city', 'about_me', 'last_visit', 'created_at', 'profile_hidden', 'url_avatar', 'wants_notification_comment_quote', 'is_admin', 'total_comments', 'favorite_count', 'added_fav_count', 'published_quotes_count', 'is_subscribed_to_daily', 'is_subscribed_to_weekly'];
	protected $contentType = 'users';
	protected $embedsRelation = ['newsletters', 'country'];

	public function setUp()
	{
		parent::setUp();

		$this->controller = App::make('TeenQuotes\Api\V1\Controllers\UsersController');

		Factory::times($this->nbRessources)->create('TeenQuotes\Users\Models\User');
		
		$this->attachCountryForAllUsers();
	}

	/**
	 * @expectedException Laracasts\Validation\FormValidationException
	 * @expectedExceptionMessage The login must be at least 3 characters.
	 */
	public function testStoreSmallLogin()
	{
		$this->addInputReplace([
			'login'    => 'a',
			'email'    => 'bob@example.com',
			'password' => 'azerty'
		]);

		$this->tryStore();
	}

	/**
	 * @expectedException Laracasts\Validation\FormValidationException
	 * @expectedExceptionMessage The login has already been taken.
	 */
	public function testStoreAlreadyTakenLogin()
	{
		$u = User::find(1);

		$this->addInputReplace([
			'login'    => $u['login'],
			'email'    => 'bob@example.com',
			'password' => 'azerty'
		]);

		$this->tryStore();
	}

	/**
	 * @expectedException Laracasts\Validation\FormValidationException
	 * @expectedExceptionMessage The login may only contain letters, numbers, and dashes.
	 */
	public function testStoreWrongLoginFormat()
	{
		$this->addInputReplace([
			'login'    => '!$$$',
			'email'    => 'bob@example.com',
			'password' => 'azerty'
		]);

		$this->tryStore();
	}

	/**
	 * @expectedException Laracasts\Validation\FormValidationException
	 * @expectedExceptionMessage The login may not be greater than 20 characters.
	 */
	public function testStoreLongLogin()
	{
		$this->addInputReplace([
			'login'    => $this->generateString(21),
			'email'    => 'bob@example.com',
			'password' => 'azerty'
		]);

		$this->tryStore();
	}

	/**
	 * @expectedException Laracasts\Validation\FormValidationException
	 * @expectedExceptionMessage The password must be at least 6 characters.
	 */
	public function testStoreWrongPassword()
	{
		$this->addInputReplace([
			'login'    => $this->generateString(10),
			'email'    => 'bob@example.com',
			'password' => 'azert'
		]);

		$this->tryStore();
	}

	/**
	 * @expectedException Laracasts\Validation\FormValidationException
	 * @expectedExceptionMessage The email must be a valid email address.
	 */
	public function testStoreWrongEmail()
	{
		$this->addInputReplace([
			'login'    => $this->generateString(10),
			'email'    => 'bob',
			'password' => 'azerty'
		]);

		$this->tryStore();
	}

	/**
	 * @expectedException Laracasts\Validation\FormValidationException
	 * @expectedExceptionMessage The email has already been taken.
	 */
	public function testStoreAlreadyTakenEmail()
	{
		$u = User::find(1);

		$this->addInputReplace([
			'login'    => $this->generateString(10),
			'email'    => $u['email'],
			'password' => 'azerty'
		]);

		$this->tryStore();
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

	/**
	 * @expectedException Laracasts\Validation\FormValidationException
	 * @expectedExceptionMessage The password must be at least 6 characters.
	 */
	public function testPutPasswordTooSmall()
	{
		$this->addInputReplace([
			'password'              => 'azert',
			'password_confirmation' => 'azert',
		]);

		$this->assertPutPasswordError();
	}

	/**
	 * @expectedException Laracasts\Validation\FormValidationException
	 * @expectedExceptionMessage The password confirmation does not match.
	 */
	public function testPutPasswordNotSame()
	{
		$this->addInputReplace([
			'password'              => 'azerty',
			'password_confirmation' => 'azert',
		]);

		$this->assertPutPasswordError();
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

	/**
	 * @expectedException Laracasts\Validation\FormValidationException
	 * @expectedExceptionMessage The selected gender is invalid.
	 */
	public function testPutProfileWrongGender()
	{
		$this->addInputReplace([
			'gender' => 'foo',
		]);

		$this->assertPutProfileError();
	}

	/**
	 * @expectedException Laracasts\Validation\FormValidationException
	 * @expectedExceptionMessage The birthdate does not match the format Y-m-d.
	 */
	public function testPutProfileWrongBirthdate()
	{
		$this->addInputReplace([
			'gender'    => 'M',
			'birthdate' => '1975-01-32'
		]);

		$this->assertPutProfileError();
	}

	/**
	 * @expectedException Laracasts\Validation\FormValidationException
	 * @expectedExceptionMessage The selected country was not found.
	 */
	public function testPutProfileWrongCountry()
	{
		$this->addInputReplace([
			'gender'    => 'M',
			'birthdate' => '1975-01-25',
			'country'   => Country::all()->count() + 1
		]);

		$this->assertPutProfileError();
	}

	/**
	 * @expectedException Laracasts\Validation\FormValidationException
	 * @expectedExceptionMessage The about me may not be greater than 500 characters.
	 */
	public function testPutProfileWrongAboutMe()
	{
		$this->addInputReplace([
			'gender'    => 'M',
			'birthdate' => '1975-01-25',
			'country'   => Country::first()->id,
			'about_me'  => $this->generateString(501)
		]);

		$this->assertPutProfileError();
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
		$newColor = 'foo';
		$this->assertFalse(in_array($newColor, Config::get('app.users.colorsAvailableQuotesPublished')));
		
		$this->addInputReplace([
			'notification_comment_quote' => true,
			'hide_profile'               => true,
			'weekly_newsletter'          => true,
			'daily_newsletter'           => true,
			'colors'                     => $newColor
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
		$this->assertTrue(in_array($newColor, Config::get('app.users.colorsAvailableQuotesPublished')));

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

	/**
	 * @expectedException        TeenQuotes\Exceptions\ApiNotFoundException
	 * @expectedExceptionMessage users
	 */
	public function testSearchUsersNotFound()
	{
		$this->deleteAllUsers();

		// Create a single user with a non-matching login
		Factory::create('TeenQuotes\Users\Models\User', ['login' => 'abc']);

		$this->assertEquals(1, User::all()->count());

		$this->doRequest('getSearch', 'foo');
	}

	public function testSearchSuccess()
	{
		// We don't display newsletters info when searching for users
		$this->disableEmbedsNewsletter();

		$this->generateUsersWithPartialLogin('abc');

		$this->assertEquals($this->nbRessources, User::all()->count());

		// Verify that we can retrieve our users even
		// with partials login
		$this->tryFirstPage('getSearch', 'ab');
		$this->tryFirstPage('getSearch', 'a');
		$this->tryFirstPage('getSearch', 'abc');
		$this->tryFirstPage('getSearch', 'c');
	}

	/**
	 * @expectedException        TeenQuotes\Exceptions\ApiNotFoundException
	 * @expectedExceptionMessage users
	 */
	public function testSearchMatchingUsersWithHiddenProfile()
	{
		$partLogin = 'abc';
		$this->generateUsersWithPartialLogin($partLogin);

		// Hide all users profile
		User::all()->each(function($u)
		{
			$u->hide_profile = 1;
			$u->save();
		});

		// Search results should not display hidden profiles
		$this->doRequest('getSearch', $partLogin);
	}

	/**
	 * @expectedException        TeenQuotes\Exceptions\ApiNotFoundException
	 * @expectedExceptionMessage users
	 */
	public function testSearchFailsWrongPage()
	{
		$this->generateUsersWithPartialLogin('abc');

		// Go to a page where we should not find any results
		// matching our query
		$this->addInputReplace([
			'page'     => 2,
			'pagesize' => $this->nbRessources
		]);

		$this->doRequest('getSearch', 'abc');
	}

	private function generateUsersWithPartialLogin($string)
	{
		$this->deleteAllUsers();

		for ($i = 1; $i <= $this->nbRessources; $i++) {
			$login = $this->generateString(2).$string.$i;
			Factory::create('TeenQuotes\Users\Models\User', compact('login'));
		}

		$this->attachCountryForAllUsers();
	}

	private function assertPutPasswordError()
	{
		$this->logUserWithId(1);

		$this->doRequest('putPassword');
	}

	private function assertPutProfileError()
	{
		$this->logUserWithId(1);

		$this->doRequest('putProfile');
	}

	private function deleteAllUsers()
	{
		User::all()->each(function($u){
			$u->delete();
		});
	}

	private function attachCountryForAllUsers()
	{
		User::all()->each(function($u){
			$c = Factory::create('TeenQuotes\Countries\Models\Country');
			$u->country = $c['id'];
			$u->save();
		});
	}

	private function disableEmbedsNewsletter()
	{
		$this->embedsRelation = ['country'];
	}

	private function setFullAttributesAreRequired()
	{
		$this->requiredAttributes = $this->requiredFull;
	}
}