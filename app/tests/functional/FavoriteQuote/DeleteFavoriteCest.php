<?php

class DeleteFavoriteCest {
	
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
		$I->createSomePublishedQuotes();
		
		// Create a new user and a fresh published quote
		$this->user = $I->logANewUser();
		$this->firstQuote = $I->insertInDatabase(1, 'Quote', ['created_at' => Carbon::now()->addMonth(), 'user_id' => $this->user->id]);
		
		// Add to the user's favorites the first quote
		$I->addAFavoriteForUser($this->firstQuote->id, $this->user->id);
	}

	public function addAQuoteToFavorite(FunctionalTester $I)
	{
		$I->am('a member of Teen Quotes');
		$I->wantTo("remove a quote from my favorites");

		$this->goToFirstQuote($I);

		// The quote is in my favorites
		$I->seeElement('.quote i.fa-heart');
		$I->assertEquals(1, $this->firstQuote->total_favorites);
		$I->assertTrue($this->firstQuote->isFavoriteForCurrentUser());
		$I->assertEquals([$this->firstQuote->id], $this->user->arrayIDFavoritesQuotes());

		// Remove the quote from my favorites
		$I->sendAjaxPostRequest(URL::route('unfavorite', $this->firstQuote->id));
		
		// Run our assertions
		$I->assertEquals(0, $this->firstQuote->total_favorites);
		$I->assertFalse($this->firstQuote->isFavoriteForCurrentUser());
		$I->assertEquals([], $this->user->arrayIDFavoritesQuotes());
		
		// Check that the single quote offers me to add the quote to my favorites
		$I->amOnRoute('quotes.show', $this->firstQuote->id);
		$I->seeElement('.quote i.fa-heart-o');
		$I->dontSeeElement('.quote i.fa-heart');
	}

	private function goToFirstQuote(FunctionalTester $I)
	{
		$I->amOnRoute('home');

		// Go to the single quote page
		$I->click('#'.$this->firstQuote->id);
		$I->seeCurrentRouteIs('quotes.show', $this->firstQuote->id);
	}
}