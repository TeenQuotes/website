<?php

class AddFavoriteCest {

	/**
	 * The logged in user
	 * @var \TeenQuotes\Users\Models\User
	 */
	private $user;

	/**
	 * The first quote on the first page
	 * @var \TeenQuotes\Quotes\Models\Quote
	 */
	private $firstQuote;

	public function _before(FunctionalTester $I)
	{
		$I->createSomePublishedQuotes();

		// Create a new user and a fresh published quote
		$this->user = $I->logANewUser();
		$this->firstQuote = $I->insertInDatabase(1, 'Quote', ['created_at' => Carbon::now()->addMonth(), 'user_id' => $this->user->id]);
	}

	public function addAQuoteToFavorite(FunctionalTester $I)
	{
		$I->am('a member of Teen Quotes');
		$I->wantTo("add a quote to my favorites");

		$this->goToFirstQuote($I);

		// I can add the quote to my favorites
		$I->seeElement('.quote i.fa-heart-o');
		$I->assertEquals(0, $this->firstQuote->total_favorites);
		$I->assertFalse($this->firstQuote->isFavoriteForCurrentUser());

		// Add the quote to my favorites
		$I->sendAjaxPostRequest(URL::route('favorite', $this->firstQuote->id));

		// Run our assertions
		$I->assertEquals(1, $this->firstQuote->total_favorites);
		$I->assertTrue($this->firstQuote->isFavoriteForCurrentUser());

		// Check that the single quote shows that the quote is in my favorites
		$I->amOnRoute('quotes.show', $this->firstQuote->id);
		$I->dontSeeElement('.quote .favorite-action i.fa-heart-o');
		$I->seeElement('.quote .favorite-action i.fa-heart');
	}

	public function iCanNotAddAQuoteToMyFavoritesAsAGuest(FunctionalTester $I)
	{
		$I->am('a member of Teen Quotes');
		$I->wantTo("verify that I can't add a quote to my favorites");

		$I->logout();

		$this->goToFirstQuote($I);
		// Verify that we don't have any heart displayed
		$I->dontSeeElement('.quote .favorite-action i.fa-heart-o');
		$I->dontSeeElement('.quote .favorite-action i.fa-heart');
	}

	private function goToFirstQuote(FunctionalTester $I)
	{
		$I->amOnRoute('home');

		// Go to the single quote page
		$I->click('#'.$this->firstQuote->id);
		$I->seeCurrentRouteIs('quotes.show', $this->firstQuote->id);
	}
}