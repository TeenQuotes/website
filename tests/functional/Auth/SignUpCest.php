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

		// Assert that the welcome e-mail has been sent
		$I->seeInLastEmailSubject('Welcome on Teen Quotes '.$login.'!');
		$I->seeInLastEmail('We are excited to welcome you on board!');
		$I->seeInLastEmail('You can now go to your profile, you will find a nice starter kit');
	}
}