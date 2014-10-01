<?php

class SignInCest {
	
	public function _before(FunctionalTester $I)
	{
		$I->createSomePublishedQuotes();
	}

	public function performSigninFlow(FunctionalTester $I)
	{
		$I->am('a Teen Quotes member');
		$I->wantTo('sign in to my Teen Quotes account');

		$I->signIn();
		$I->amOnRoute('home');

		$I->see('Nice to see you :)', '.alert-success');
		$I->see('My profile', '.navbar');
		$I->assertTrue(Auth::check());
	}
}