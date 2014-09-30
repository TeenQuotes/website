<?php namespace Codeception\Module;

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

		$I = $this->getModule('Laravel4');

		$I->amOnRoute('signin');
		$I->fillField('Login', $login);
		$I->fillField('Password', $passwordClear);
		$I->click('Log me in!', 'form');
	}

	public function haveAnAccount($overrides = [])
	{
		$user  = TestDummy::create('User', $overrides);
	}

	public function createSomePublishedQuotes($overrides = [])
	{
		$overrides['approved'] = Quote::PUBLISHED;
		TestDummy::times(10)->create('Quote', $overrides);
	}
}