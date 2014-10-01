<?php namespace Codeception\Module;

use Illuminate\Support\Facades\Auth;
use Hash;
use Laracasts\TestDummy\Factory as TestDummy;
use Quote;

class FunctionalHelper extends \Codeception\Module
{
	public function signIn()
	{
		$login = 'foobar42';
		$passwordClear = 'azerty22';
		// Will be automatically hashed
		$password = $passwordClear;

		$this->createSomePublishedQuotes();
		$this->haveAnAccount(compact('login', 'password'));
		$this->navigateToTheSignInPage();

		$I = $this->getModule('Laravel4');

		$I->fillField('Login', $login);
		$I->fillField('Password', $passwordClear);
		$I->click('Log me in!', 'form');
	}

	public function navigateToTheSignInPage()
	{
		$I = $this->getModule('Laravel4');

		// TODO: always do this
		$this->createSomePublishedQuotes();
		
		$I->amOnRoute('home');
		$I->click('Log in');
		$I->seeCurrentRouteIs('signin');
	}

	public function navigateToTheSignUpPage()
	{
		$I = $this->getModule('Laravel4');
		
		// TODO: always do this
		$this->createSomePublishedQuotes();
		
		$I->amOnRoute('home');
		$I->click('Log in');
		$I->seeCurrentRouteIs('signin');
		$I->click('I want an account!');
		$I->seeCurrentRouteIs('signup');
	}

	public function fillRegistrationFormFor($login)
	{
		$I = $this->getModule('Laravel4');
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

	public function haveAnAccount($overrides = [])
	{
		$user = TestDummy::create('User', $overrides);
		
		return $user;
	}

	public function createSomePublishedQuotes($overrides = [])
	{
		$overrides['approved'] = Quote::PUBLISHED;
		TestDummy::times(10)->create('Quote', $overrides);
	}
}