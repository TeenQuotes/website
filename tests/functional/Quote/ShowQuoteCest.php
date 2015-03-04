<?php

class ShowQuoteCest {

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
		$I->createSomePublishedQuotes(['nb_quotes' => $I->getTotalNumberOfQuotesToCreate()]);

		// Create a new user, a fresh published quote
		$this->user = $I->logANewUser();
		$this->firstQuote = $I->insertInDatabase(1, 'Quote', ['created_at' => Carbon::now()->addMonth(), 'user_id' => $this->user->id]);

		// Add some comments to the quote
		$I->insertInDatabase($I->getNbComments(), 'Comment', ['quote_id' => $this->firstQuote->id]);

		// Associate a tag to the quote
		$tagRepo = App::make('TeenQuotes\Tags\Repositories\TagRepository');
		$loveTag = $tagRepo->create('love');
		$tagRepo->tagQuote($this->firstQuote, $loveTag);

		// Add to the user's favorites the first quote
		$I->addAFavoriteForUser($this->firstQuote->id, $this->user->id);
	}

	public function browseAQuoteWhenLoggedIn(FunctionalTester $I)
	{
		$I->am('a member of Teen Quotes');
		$I->wantTo("browse a quote when logged in");

		$this->assertSingleQuoteContainsRequiredInformation($I);

		// The quote is in my favorites
		$I->seeElement('.quote .favorite-action i.fa-heart');

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
		$I->dontSeeElement('.quote .favorite-action i.fa-heart');

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

		// We see the love tag
		$I->seeNumberOfElements('.quotes__tags-tag', 1);
		$I->see('#Love', '.quotes__tags-tag');

		// The favorites info text
		$I->see($this->user->login.' favorited this quote.', '.favorites-info');
	}
}