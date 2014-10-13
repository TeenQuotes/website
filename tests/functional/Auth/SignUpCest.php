<?php

class SignUpCest {
	
	public function _before(FunctionalTester $I)
	{
		$I->createSomePublishedQuotes();
	}

	public function performSignupFlow(FunctionalTester $I)
	{
		$I->am('a guest');
		$I->wantTo("create a Teen Quotes' account");
		
		$login = 'foobar';

		$I->navigateToTheSignUpPage();
		$I->fillRegistrationFormFor($login);
		$I->amOnMyNewProfile($login);
	}
}