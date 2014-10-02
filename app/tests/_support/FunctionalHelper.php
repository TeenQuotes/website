<?php namespace Codeception\Module;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laracasts\TestDummy\Factory as TestDummy;
use Quote;
use User;

class FunctionalHelper extends \Codeception\Module
{
	public function signIn()
	{
		$login = 'foobar42';
		$passwordClear = 'azerty22';
		// Will be automatically hashed
		$password = $passwordClear;

		$this->haveAnAccount(compact('login', 'password'));
		
		$this->navigateToTheSignInPage();
		$this->fillSigninForm($login, $passwordClear);
	}

	public function checkThatIHaveBeenLoggedIn()
	{
		$I = $this->getModule('Laravel4');
		
		$I->amOnRoute('home');
		$this->seeSuccessFlashMessage('Nice to see you :)');
		$I->see('My profile', '.navbar');
		$I->assertTrue(Auth::check());
	}

	public function navigateToTheSignInPage()
	{
		$I = $this->getModule('Laravel4');
		
		$I->amOnRoute('home');
		$I->click('Log in');
		$I->seeCurrentRouteIs('signin');
	}

	public function navigateToTheSignUpPage()
	{
		$I = $this->getModule('Laravel4');
				
		$I->amOnRoute('home');
		$I->click('Log in');
		$I->seeCurrentRouteIs('signin');
		$I->click('I want an account!');
		$I->seeCurrentRouteIs('signup');
	}

	public function navigateToTheAddQuotePage()
	{
		$I = $this->getModule('Laravel4');
				
		$I->amOnRoute('home');
		$I->click('Add your quote');
		$I->seeCurrentRouteIs('addquote');
	}

	public function performLogoutFlow()
	{
		$I = $this->getModule('Laravel4');
		
		$this->navigateToMyProfile();
		$I->click('Log out');
	}

	public function fillSigninForm($login, $password)
	{
		$I = $this->getModule('Laravel4');
		
		$I->fillField('Login', $login);
		$I->fillField('Password', $password);
		$I->click('Log me in!', 'form');
	}

	private function fillAddQuoteForm()
	{
		$I = $this->getModule('Laravel4');
		
		$I->fillField('#content-quote', Str::random(150));
		$I->click('Submit my quote!');
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

	public function navigateToMyProfile()
	{
		$I = $this->getModule('Laravel4');
		
		$u = Auth::user();
		$I->amOnRoute('users.show', $u->login);
	}

	public function navigateToMyEditProfilePage()
	{
		$I = $this->getModule('Laravel4');
		$u = Auth::user();

		$this->navigateToMyProfile();

		$I->click('Edit my profile');
		
		// Assert that we can do several actions
		$I->seeCurrentRouteIs('users.edit', $u->login);
		$I->seeInTitle('Edit your profile');
		$I->see('Edit my profile');
		$I->see('Change my password');
		$I->see('Edit my settings');
		$I->see('Delete my account');
	}

	/**
	 * Fill the "update my password" form on the user's profile
	 * @param  string $password       The new password
	 * @param  string $passwordRepeat The new repeated password
	 */
	public function fillChangePasswordForm($password, $passwordRepeat)
	{
		$I = $this->getModule('Laravel4');

		$I->fillField('New password', $password);
		$I->fillField('Confirm your password', $passwordRepeat);
		$I->click('Change my password!');
	}

	public function assertPasswordHasBeenChanged()
	{
		$I = $this->getModule('Laravel4');
		$u = Auth::user();
		
		$I->seeCurrentRouteIs('users.edit', $u->login);
		$this->seeSuccessFlashMessage('Your password has been changed');
	}

	public function navigateToTheResetPasswordPage()
	{
		$I = $this->getModule('Laravel4');
		
		$this->navigateToTheSignInPage();
		$I->click("I don't remember my password!");
		$I->seeCurrentRouteIs('passwordReminder');
	}

	public function submitANewQuote()
	{
		$I = $this->getModule('Laravel4');

		$this->navigateToTheAddQuotePage();

		$oldNbWaitingQuotes = $this->numberWaitingQuotesForUser();
		
		$this->fillAddQuoteForm();

		$I->amOnRoute('home');
		$this->seeSuccessFlashMessage('Your quote has been submitted');

		$currentNbWaitingQuotes = $this->numberWaitingQuotesForUser();
		
		// Assert that the quote was added to the DB
		$I->assertEquals($oldNbWaitingQuotes + 1, $currentNbWaitingQuotes);
	}

	public function cantSubmitANewQuote()
	{
		$I = $this->getModule('Laravel4');
		
		$this->navigateToTheAddQuotePage();
		
		$oldNbWaitingQuotes = $this->numberWaitingQuotesForUser();
		
		$this->fillAddQuoteForm();
		
		$I->amOnRoute('addquote');
		$I->see('You have submitted enough quotes for today');

		$currentNbWaitingQuotes = $this->numberWaitingQuotesForUser();
		
		// Assert that the quote was not added to the DB
		$I->assertEquals($oldNbWaitingQuotes, $currentNbWaitingQuotes);
	}

	public function fillRegistrationFormFor($login)
	{
		$I = $this->getModule('Laravel4');
		
		// Set a dummy IP address
		$_SERVER['REMOTE_ADDR'] = '200.22.22.22';
		
		$I->seeInTitle('Create an account');
		$I->see('Create your account');
		$I->fillField('#login-signup', $login);
		$I->fillField('#email-signup', $login.'@yahoo.com');
		$I->fillField('#password', 'azerty22');
		$I->click("#submit-form");
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
	 * Fill the password reset form for a given user
	 * @param  User   $u The given user
	 */
	public function fillPasswordResetFormFor(User $u)
	{
		$I = $this->getModule('Laravel4');
		
		$I->fillField('#email', $u->email);
		$I->click('Reset my password!');
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

	/**
	 * Create a new user and store it in database. Can pass an array (key-value) to override dummy values
	 * @param  array $overrides The key-value array used to override dummy values
	 * @return User The created user instance
	 */
	public function haveAnAccount($overrides = [])
	{
		$user = TestDummy::create('User', $overrides);
		
		return $user;
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

	public function createSomePublishedQuotes($overrides = [])
	{
		$overrides['approved'] = Quote::PUBLISHED;
		
		TestDummy::times(10)->create('Quote', $overrides);
	}
}