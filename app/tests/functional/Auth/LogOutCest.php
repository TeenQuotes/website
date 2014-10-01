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
		
		$I->navigateToMyProfile();
		$I->click('Log out');

		$I->see('You have been logged out.', '.alert-success');
		$I->assertFalse(Auth::check());
	}
}