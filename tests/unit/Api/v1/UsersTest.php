<?php

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use TeenQuotes\Countries\Models\Country;
use TeenQuotes\Users\Models\User;

class UsersTest extends ApiTest {

	protected $requiredAttributes = ['id', 'login', 'email', 'last_visit', 'created_at', 'profile_hidden', 'url_avatar', 'wants_notification_comment_quote', 'is_admin'];
	protected $requiredFull = ['id', 'login', 'email', 'birthdate', 'gender', 'country', 'city', 'about_me', 'last_visit', 'created_at', 'profile_hidden', 'url_avatar', 'wants_notification_comment_quote', 'is_admin', 'total_comments', 'favorite_count', 'added_fav_count', 'published_quotes_count', 'is_subscribed_to_daily', 'is_subscribed_to_weekly'];
	protected $embedsRelation = ['newsletters', 'country'];

	protected function _before()
	{
		parent::_before();

		$this->unitTester->setController(App::make('TeenQuotes\Api\V1\Controllers\UsersController'));

		$this->unitTester->setContentType('users');

		$this->unitTester->insertInDatabase($this->unitTester->getNbRessources(), 'User');

		$this->attachCountryForAllUsers();
	}

	/**
	 * @expectedException Laracasts\Validation\FormValidationException
	 * @expectedExceptionMessage The login must be at least 3 characters.
	 */
	public function testStoreSmallLogin()
	{
		$this->unitTester->addInputReplace([
			'login'    => 'a',
			'email'    => 'bob@example.com',
			'password' => 'azerty'
		]);

		$this->unitTester->tryStore();
	}

	/**
	 * @expectedException Laracasts\Validation\FormValidationException
	 * @expectedExceptionMessage The login has already been taken.
	 */
	public function testStoreAlreadyTakenLogin()
	{
		$u = User::find(1);

		$this->unitTester->addInputReplace([
			'login'    => $u['login'],
			'email'    => 'bob@example.com',
			'password' => 'azerty'
		]);

		$this->unitTester->tryStore();
	}

	/**
	 * @expectedException Laracasts\Validation\FormValidationException
	 * @expectedExceptionMessage The login may only contain letters, numbers, and dashes.
	 */
	public function testStoreWrongLoginFormat()
	{
		$this->unitTester->addInputReplace([
			'login'    => '!$$$',
			'email'    => 'bob@example.com',
			'password' => 'azerty'
		]);

		$this->unitTester->tryStore();
	}

	/**
	 * @expectedException Laracasts\Validation\FormValidationException
	 * @expectedExceptionMessage The login may not be greater than 20 characters.
	 */
	public function testStoreLongLogin()
	{
		$this->unitTester->addInputReplace([
			'login'    => $this->unitTester->generateString(21),
			'email'    => 'bob@example.com',
			'password' => 'azerty'
		]);

		$this->unitTester->tryStore();
	}

	/**
	 * @expectedException Laracasts\Validation\FormValidationException
	 * @expectedExceptionMessage The password must be at least 6 characters.
	 */
	public function testStoreWrongPassword()
	{
		$this->unitTester->addInputReplace([
			'login'    => $this->unitTester->generateString(10),
			'email'    => 'bob@example.com',
			'password' => 'azert'
		]);

		$this->unitTester->tryStore();
	}

	/**
	 * @expectedException Laracasts\Validation\FormValidationException
	 * @expectedExceptionMessage The email must be a valid email address.
	 */
	public function testStoreWrongEmail()
	{
		$this->unitTester->addInputReplace([
			'login'    => $this->unitTester->generateString(10),
			'email'    => 'bob',
			'password' => 'azerty'
		]);

		$this->unitTester->tryStore();
	}

	/**
	 * @expectedException Laracasts\Validation\FormValidationException
	 * @expectedExceptionMessage The email has already been taken.
	 */
	public function testStoreAlreadyTakenEmail()
	{
		$u = User::find(1);

		$this->unitTester->addInputReplace([
			'login'    => $this->unitTester->generateString(10),
			'email'    => $u['email'],
			'password' => 'azerty'
		]);

		$this->unitTester->tryStore();
	}

	public function testStoreSuccess()
	{
		$this->unitTester->addInputReplace([
			'login'    => $this->unitTester->generateString(10),
			'email'    => 'bob@example.com',
			'password' => 'azerty'
		]);

		// Set a fake IP
		$_SERVER['REMOTE_ADDR'] = '22.22.22.22';

		$this->unitTester->tryStore()
			->assertStatusCodeIs(Response::HTTP_CREATED)
			->assertResponseHasRequiredAttributes();

		// Check that the user has been subscribed to the weekly newsletter
		$u = User::find($this->unitTester->getNbRessources() + 1);
		$this->assertTrue($u->getIsSubscribedToWeekly());
		$this->assertFalse($u->getIsSubscribedToDaily());

		// TODO: assert that the welcome e-mail was sent
	}

	public function testDeleteSuccess()
	{
		$idUser = 1;
		$this->unitTester->logUserWithId($idUser);

		$this->unitTester->doRequest('destroy')
			->assertStatusCodeIs(Response::HTTP_OK)
			->withStatusMessage('user_deleted')
			->withSuccessMessage('The user has been deleted.');

		$this->assertEmpty(User::find($idUser));
	}

	public function testGetInfoLoggedInUser()
	{
		$idUser = 1;
		$this->unitTester->logUserWithId($idUser);

		$this->setFullAttributesAreRequired();

		$this->unitTester->doRequest('getUsers')
			->assertStatusCodeIs(Response::HTTP_OK)
			->assertResponseMatchesExpectedSchema();

		$this->unitTester->assertResponseKeyIs('id', $idUser);
	}

	public function testUserShowNotFound()
	{
		$this->unitTester->tryShowNotFound()
			->withStatusMessage(404)
			->withErrorMessage('User not found.');
	}

	public function testShowFound()
	{
		$this->setFullAttributesAreRequired();

		for ($i = 1; $i <= $this->unitTester->getNbRessources() ; $i++)
			$this->unitTester->tryShowFound($i);
	}

	/**
	 * @expectedException Laracasts\Validation\FormValidationException
	 * @expectedExceptionMessage The password must be at least 6 characters.
	 */
	public function testPutPasswordTooSmall()
	{
		$this->unitTester->addInputReplace([
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
		$this->unitTester->addInputReplace([
			'password'              => 'azerty',
			'password_confirmation' => 'azert',
		]);

		$this->assertPutPasswordError();
	}

	public function testPutPasswordSuccess()
	{
		$newPassword = 'azerty';
		$idUser = 1;

		$this->unitTester->addInputReplace([
			'password'              => $newPassword,
			'password_confirmation' => $newPassword,
		]);

		$u = $this->unitTester->logUserWithId($idUser);

		$this->unitTester->doRequest('putPassword')
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
		$this->unitTester->addInputReplace([
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
		$this->unitTester->addInputReplace([
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
		$this->unitTester->addInputReplace([
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
		$this->unitTester->addInputReplace([
			'gender'    => 'M',
			'birthdate' => '1975-01-25',
			'country'   => Country::first()->id,
			'about_me'  => $this->unitTester->generateString(501)
		]);

		$this->assertPutProfileError();
	}

	// TODO: write tests for uploaded files

	public function testPutProfileSuccess()
	{
		$gender = 'M';
		$birthdate = '1975-01-25';
		$country = Country::first()->id;
		$about_me = $this->unitTester->generateString(200);

		$data = compact('gender', 'birthdate', 'country', 'about_me');
		$this->unitTester->addInputReplace($data);

		$this->unitTester->logUserWithId(1);

		$this->unitTester->doRequest('putProfile')
			->assertStatusCodeIs(Response::HTTP_OK)
			->withStatusMessage('profile_updated')
			->withSuccessMessage('The profile has been updated.');
	}

	public function testPutSettingsWrongColor()
	{
		$newColor = 'foo';
		$this->assertFalse(in_array($newColor, Config::get('app.users.colorsAvailableQuotesPublished')));

		$this->unitTester->addInputReplace([
			'notification_comment_quote' => true,
			'hide_profile'               => true,
			'weekly_newsletter'          => true,
			'daily_newsletter'           => true,
			'colors'                     => $newColor
		]);

		$this->unitTester->logUserWithId(1);

		$this->unitTester->doRequest('putSettings')
			->assertStatusCodeIs(Response::HTTP_BAD_REQUEST)
			->withStatusMessage('wrong_color')
			->withErrorMessage('This color is not allowed.');
	}

	public function testPutSettingsSuccess()
	{
		$u = $this->unitTester->logUserWithId(1);

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

		$this->unitTester->addInputReplace([
			'notification_comment_quote' => false,
			'hide_profile'               => true,
			'weekly_newsletter'          => false,
			'daily_newsletter'           => true,
			'colors'                     => $newColor
		]);

		$this->unitTester->doRequest('putSettings')
			->assertStatusCodeIs(Response::HTTP_OK)
			->withStatusMessage('profile_updated')
			->withSuccessMessage('The profile has been updated.');

		// Check that values have changed accordingly
		$this->assertFalse($u->getIsSubscribedToWeekly());
		$this->assertTrue($u->getIsSubscribedToDaily());
		$this->assertTrue($u->isHiddenProfile());
		$this->assertFalse($u->wantsEmailComment());
	}

	/**
	 * @expectedException        TeenQuotes\Exceptions\ApiNotFoundException
	 * @expectedExceptionMessage users
	 */
	public function testSearchUsersNotFound()
	{
		$this->deleteAllUsers();

		// Create a single user with a non-matching login
		$this->unitTester->insertInDatabase(1, 'User', ['login' => 'abc']);

		$this->assertEquals(1, User::all()->count());

		$this->unitTester->doRequest('getSearch', 'foo');
	}

	public function testSearchSuccess()
	{
		// We don't display newsletters info when searching for users
		$this->disableEmbedsNewsletter();

		$this->generateUsersWithPartialLogin('abc');

		$this->assertEquals($this->unitTester->getNbRessources(), User::all()->count());

		// Verify that we can retrieve our users even
		// with partials login
		$this->unitTester->tryFirstPage('getSearch', 'ab');
		$this->unitTester->tryFirstPage('getSearch', 'a');
		$this->unitTester->tryFirstPage('getSearch', 'abc');
		$this->unitTester->tryFirstPage('getSearch', 'c');
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
		$this->unitTester->doRequest('getSearch', $partLogin);
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
		$this->unitTester->addInputReplace([
			'page'     => 2,
			'pagesize' => $this->unitTester->getNbRessources()
		]);

		$this->unitTester->doRequest('getSearch', 'abc');
	}

	private function generateUsersWithPartialLogin($string)
	{
		$this->deleteAllUsers();

		for ($i = 1; $i <= $this->unitTester->getNbRessources(); $i++) {
			$login = $this->unitTester->generateString(2).$string.$i;
			$this->unitTester->insertInDatabase(1, 'User', compact('login'));
		}

		$this->attachCountryForAllUsers();
	}

	private function assertPutPasswordError()
	{
		$this->unitTester->logUserWithId(1);

		$this->unitTester->doRequest('putPassword');
	}

	private function assertPutProfileError()
	{
		$this->unitTester->logUserWithId(1);

		$this->unitTester->doRequest('putProfile');
	}

	private function deleteAllUsers()
	{
		User::all()->each(function($u){
			$u->delete();
		});
	}

	private function attachCountryForAllUsers()
	{
		$instance = $this;

		User::all()->each(function($u) use ($instance) {
			$c = $instance->unitTester->insertInDatabase(1, 'Country');
			$u->country = $c['id'];
			$u->save();
		});
	}

	private function disableEmbedsNewsletter()
	{
		$this->unitTester->setEmbedsRelation(['country']);
	}

	private function setFullAttributesAreRequired()
	{
		$this->unitTester->setRequiredAttributes($this->requiredFull);
	}
}