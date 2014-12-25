<?php

class AddQuoteCest {

	public function _before(FunctionalTester $I)
	{
		$I->logANewUser();
		$I->createSomePublishedQuotes();
	}

	public function submitAQuote(FunctionalTester $I)
	{
		$I->am('a member of Teen Quotes');
		$I->wantTo("submit a new quote");

		$I->submitANewQuote();
	}

	public function submitTooMuchQuotes(FunctionalTester $I)
	{
		$I->am('a member of Teen Quotes');
		$I->wantTo("submit too much quotes for today");

		// Submit the maximum allowed number of quotes per day
		for ($i = 1; $i <= $this->maxPublishQuotesPerDay(); $i++)
			$I->submitANewQuote();

		// Try to add another quote, we should exceed the quota
		$I->cantSubmitANewQuote();
	}

	public function aGuestShouldBeRedirectedToTheSigninPage(FunctionalTester $I)
	{
		$I->am('a guest');
		$I->wantTo("submit a new quote as a guest");

		$I->logout();

		// Navigate to the "add your quote" page
		$I->amOnRoute('home');
		$I->click('Add your quote');

		// Assert that I should be redirected to the signin / signup page
		$I->amOnRoute('signin');
		// With a friendly message :)
		$I->see('Before adding a quote, you need to be logged in.', "#addquote-warning");
	}

	private function maxPublishQuotesPerDay()
	{
		return Config::get('app.quotes.maxSubmitPerDay');
	}
}