<?php

class LogOutCest {
	
	public function _before(FunctionalTester $I)
	{
		$I->createSomePublishedQuotes();
	}

	public function clickOnLogoutOnProfile(FunctionalTester $I)
	{
		$I->am('a Teen Quotes member');
		$I->wantTo('log out from my account');

		$I->logANewUser();
		
		$I->performLogoutFlow();

		$I->seeSuccessFlashMessage('You have been logged out.');
		$I->assertFalse(Auth::check());
	}
}