<?php namespace Codeception\Module;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laracasts\TestDummy\Factory as TestDummy;
use Quote;
use User;

class FunctionalHelper extends \Codeception\Module
{
	
	/**
	 * Fill the sign in form and log in
	 * @param  string $login         
	 * @param  string $passwordClear
	 */
	public function signIn($login, $passwordClear)
	{
		// Will be automatically hashed
		$password = $passwordClear;

		$this->haveAnAccount(compact('login', 'password'));
		
		$this->getModule('NavigationHelper')->navigateToTheSignInPage();
		$this->getModule('FormFillerHelper')->fillSigninForm($login, $passwordClear);
	}

	public function checkThatIHaveBeenLoggedIn()
	{
		$I = $this->getModule('Laravel4');
		
		$I->amOnRoute('home');
		$this->seeSuccessFlashMessage('Nice to see you :)');
		$I->see('My profile', '.navbar');
		$I->assertTrue(Auth::check());
	}

	public function assertMySettingsHaveDefaultValues()
	{
		$this->assertMySettingsHaveTheseValues([
			'color'                      => $this->getDefaultColorForPublishedQuotes(),
			// Receive a notification if a comment is posted on one of my quotes
			'notification_comment_quote' => 1,
			// Profile not hidden
			'hide_profile'               => 0,
			// Subscribed to the weekly newsletter
			'daily_newsletter'           => 0,
			'weekly_newsletter'          => 1
		]);
	}

	/**
	 * Get the default color for published quotes on a user's profile
	 * @return string
	 */
	private function getDefaultColorForPublishedQuotes()
	{
		return ucfirst(Config::get('app.users.defaultColorQuotesPublished'));
	}

	public function assertMySettingsHaveTheseValues(array $params)
	{
		$I = $this->getModule('Laravel4');
		
		$I->seeOptionIsSelected('select[name=colors]', $params['color']);
		
		foreach (['notification_comment_quote', 'hide_profile', 'daily_newsletter', 'weekly_newsletter'] as $value) {
			if ($params[$value] == 1)
				$I->seeCheckboxIsChecked('input[name='.$value.']');
			else 
				$I->dontSeeCheckboxIsChecked('input[name='.$value.']');
		}
	}

	public function hideProfileForCurrentUser()
	{
		$this->getModule('NavigationHelper')->navigateToMyEditProfilePage();

		$this->getModule('FormFillerHelper')->fillUserSettingsForm([
			'notification_comment_quote' => 1,
			'hide_profile'               => 1,
			'daily_newsletter'           => 0,
			'weekly_newsletter'          => 1,
			'color'                      => $this->getDefaultColorForPublishedQuotes(),
		]);
	}

	public function performLogoutFlow()
	{
		$I = $this->getModule('Laravel4');
		
		$this->getModule('NavigationHelper')->navigateToMyProfile();
		$I->click('Log out');
	}

	/**
	 * Count the number of quotes waiting moderation for a user
	 * @param  User $u The user. If null, use the authenticated user
	 * @return int The number of quotes waiting moderation for the user
	 */
	private function numberWaitingQuotesForUser(User $u = null)
	{
		if (is_null($u))
			$u = Auth::user();

		return Quote::forUser($u)
			->waiting()
			->count();
	}

	public function assertPasswordHasBeenChanged()
	{
		$I = $this->getModule('Laravel4');
		$u = Auth::user();
		
		$I->seeCurrentRouteIs('users.edit', $u->login);
		$this->seeSuccessFlashMessage('Your password has been changed');
	}

	public function submitANewQuote()
	{
		$I = $this->getModule('Laravel4');

		$this->getModule('NavigationHelper')->navigateToTheAddQuotePage();

		$oldNbWaitingQuotes = $this->numberWaitingQuotesForUser();
		
		$this->getModule('FormFillerHelper')->fillAddQuoteForm();

		$I->amOnRoute('home');
		$this->seeSuccessFlashMessage('Your quote has been submitted');

		$currentNbWaitingQuotes = $this->numberWaitingQuotesForUser();
		
		// Assert that the quote was added to the DB
		$I->assertEquals($oldNbWaitingQuotes + 1, $currentNbWaitingQuotes);
	}

	public function cantSubmitANewQuote()
	{
		$I = $this->getModule('Laravel4');
		
		$this->getModule('NavigationHelper')->navigateToTheAddQuotePage();
		
		$oldNbWaitingQuotes = $this->numberWaitingQuotesForUser();
		
		$this->getModule('FormFillerHelper')->fillAddQuoteForm();
		
		$I->amOnRoute('addquote');
		$I->see('You have submitted enough quotes for today');

		$currentNbWaitingQuotes = $this->numberWaitingQuotesForUser();
		
		// Assert that the quote was not added to the DB
		$I->assertEquals($oldNbWaitingQuotes, $currentNbWaitingQuotes);
	}

	public function amOnMyNewProfile($login)
	{
		$I = $this->getModule('Laravel4');

		$I->amOnRoute('users.show', $login);
		$I->assertTrue(Auth::check());
		$I->seeRecord('users', compact('login'));
		$I->seeElement('#welcome-profile');
	}

	/**
	 * Assert that I can see an error message on a form
	 * @param  string $message The expected message
	 */
	public function seeFormError($message)
	{
		$I = $this->getModule('Laravel4');
		
		$I->see($message, '.error-form');
	}

	/**
	 * Assert that we can see a success alert with a given message
	 * @param  string $message The expected message
	 */
	public function seeSuccessFlashMessage($message)
	{
		$I = $this->getModule('Laravel4');
		
		$I->see($message, '.alert-success');
	}

	public function assertEditProfileFormIsFilledWith(array $params)
	{
		$I = $this->getModule('Laravel4');

		$I->seeOptionIsSelected('input[name=gender]', $params['gender']);
		$I->see($params['birthdate']);
		$I->seeOptionIsSelected('select[name=country]', $params['country_name']);
		$I->see($params['city']);
		$I->see($params['about_me']);
	}

	/**
	 * Assert that the logged in user has got the given key-values pairs for its profile
	 * @param  array  $params The key-values pairs. Required keys: gender, birthdate (YYYY-MM-DD), country_name, city, about_me
	 */
	public function assertProfileHasBeenChangedWithParams(array $params)
	{
		$I = $this->getModule('Laravel4');
		
		$I->seeCurrentRouteIs('users.edit', Auth::user()->login);
		$this->seeSuccessFlashMessage('You have a brand new profile');

		$this->getModule('NavigationHelper')->navigateToMyProfile();

		$I->see($params['country_name']);
		$I->see($params['city']);
		$I->see($params['about_me']);
		$age = $this->computeAgeFromYYYMMDD($params['birthdate']);
		$I->see($age.' y/o');

		if ($params['gender'] == 'M')
			$I->see("I'm a man");
		else
			$I->see("I'm a woman");
	}

	/**
	 * Compute the age from a date formatted as YYYY-MM-DD
	 * @param  string $date The date formatted as YYYY-MM-DD
	 * @return int The age
	 */
	private function computeAgeFromYYYMMDD($date)
	{
		// Create an array to have year, month and day
		$parts = explode('-', $date);
		
		return Carbon::createFromDate($parts[0], $parts[1], $parts[2])->age;
	}

	/**
	 * Create a new user and store it in database. Can pass an array (key-value) to override dummy values
	 * @param  array $overrides The key-value array used to override dummy values
	 * @return User The created user instance
	 */
	public function haveAnAccount($overrides = [])
	{		
		return $this->insertInDatabase(1, 'User', $overrides);
	}

	/**
	 * Create a new user. Can pass an array (key-value) to override dummy values
	 * @param  array $overrides The key-value array used to override dummy values
	 * @return User The created user instance
	 */
	public function buildUser($overrides = [])
	{
		$user = TestDummy::build('User', $overrides);
		
		return $user;
	}

	/**
	 * Log a new user. Can pass an array (key-value) to override dummy values
	 * @param  array $overrides The key-value array used to override dummy values
	 * @return User The logged in user
	 */
	public function logANewUser($overrides = [])
	{
		$u = $this->haveAnAccount($overrides);

		Auth::loginUsingId($u->id);

		return $u;
	}

	/**
	 * Insert a record in database
	 * @param  int $times The number of elements to insert
	 * @param  string $class The name of the class to insert
	 * @param  array $overrides The key-value array used to override dummy values
	 * @return array|object The created record(s)
	 */
	public function insertInDatabase($times, $class, $overrides)
	{
		return TestDummy::times($times)->create($class, $overrides);
	}

	/**
	 * Add a quote to the favorites of a user
	 * @param int $quote_id The ID of the quote
	 * @param int $user_id  The user ID
	 */
	public function addAFavoriteForUser($quote_id, $user_id)
	{
		return $this->insertInDatabase(1, 'FavoriteQuote', ['quote_id' => $quote_id, 'user_id' => $user_id]);
	}

	/**
	 * Create some published quotes
	 * @param  array $overrides The key-value array used to override dummy values. If the key nb_quotes is given, specifies the number of quotes to create
	 * @return array The created quotes
	 */
	public function createSomePublishedQuotes($overrides = [])
	{
		$overrides['approved'] = Quote::PUBLISHED;

		if (array_key_exists('nb_quotes', $overrides)) {
			$nbQuotes = $overrides['nb_quotes'];
			unset($overrides['nb_quotes']);
		}
		else
			$nbQuotes = 10;
		
		return $this->insertInDatabase($nbQuotes, 'Quote', $overrides);
	}
}