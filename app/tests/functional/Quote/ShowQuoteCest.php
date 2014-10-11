<?php

class ShowQuoteCest {
	
	/**
	 * The logged in user
	 * @var User
	 */
	private $user;

	/**
	 * The first quote on the first page
	 * @var Quote
	 */
	private $firstQuote;

	public function _before(FunctionalTester $I)
	{
		$I->createSomePublishedQuotes(['nb_quotes' => $I->getTotalNumberOfQuotesToCreate()]);
		
		// Create a new user, a fresh published quote and some comments to it
		$this->user = $I->logANewUser();
		$this->firstQuote = $I->insertInDatabase(1, 'Quote', ['created_at' => Carbon::now()->addMonth(), 'user_id' => $this->user->id]);
		$I->insertInDatabase($I->getNbComments(), 'TeenQuotes\Comments\Models\Comment', ['quote_id' => $this->firstQuote->id]);

		// Add to the user's favorites the first quote
		$I->addAFavoriteForUser($this->firstQuote->id, $this->user->id);
	}

	public function browseAQuoteWhenLoggedIn(FunctionalTester $I)
	{
		$I->am('a member of Teen Quotes');
		$I->wantTo("browse a quote when logged in");

		$this->assertSingleQuoteContainsRequiredInformation($I);

		// The quote is in my favorites
		$I->seeElement('.quote i.fa-heart');

		// I can add a comment
		$I->seeElement('form.form-horizontal');
	}

	public function browseAQuoteWhenNotLoggedIn(FunctionalTester $I)
	{
		$I->am('a guest');
		$I->wantTo("browse a quote as a guest");

		$I->performLogoutFlow();
		$this->assertSingleQuoteContainsRequiredInformation($I);

		// It offers me to log in / sign up
		$I->see('I have an account!');
		$I->see('I want an account!');
		
		// The quote is not in my favorites
		$I->dontSeeElement('.quote i.fa-heart');
		
		// I can't add a comment
		$I->dontSeeElement('form.form-horizontal');
	}

	private function assertSingleQuoteContainsRequiredInformation(FunctionalTester $I)
	{		
		$I->amOnRoute('home');

		// Go to the single quote page
		$I->click('#'.$this->firstQuote->id);
		$I->seeCurrentRouteIs('quotes.show', $this->firstQuote->id);
		$I->seeInTitle('Quote #'.$this->firstQuote->id);
		
		// We have got the right number of comments
		$I->see($I->getNbComments(). ' comments', '.quote');
		$I->seeNumberOfElements('.comment', $I->getNbComments());
		
		// The favorites info text
		$I->see($this->user->login.' favorited this quote.', '.favorites-info');
	}
}