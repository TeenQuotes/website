<?php

class IndexQuoteCest {
	
	/**
	 * The logged in user
	 * @var User
	 */
	private $user;

	/**
	 * The first quote on the first page
	 * @var TeenQuotes\Quotes\Models\Quote
	 */
	private $firstQuote;

	public function _before(FunctionalTester $I)
	{
		$I->createSomePublishedQuotes(['nb_quotes' => $I->getTotalNumberOfQuotesToCreate()]);

		// Create a new user, a fresh published quote and some comments to it
		$this->user = $I->logANewUser();
		$this->firstQuote = $I->insertInDatabase(1, 'TeenQuotes\Quotes\Models\Quote', ['created_at' => Carbon::now()->addMonth(), 'user_id' => $this->user->id]);
		$I->insertInDatabase($I->getNbComments(), 'TeenQuotes\Comments\Models\Comment', ['quote_id' => $this->firstQuote->id]);
	}

	public function browseLastQuotesOnHomepage(FunctionalTester $I)
	{
		$I->am('a member of Teen Quotes');
		$I->wantTo("browse last quotes");

		// Try the homepage
		$I->amOnRoute('home');
		$this->assertPageOfQuotesContainsRequiredElements($I);
	}

	public function browseLastRandomQuotes(FunctionalTester $I)
	{
		$I->am('a member of Teen Quotes');
		$I->wantTo("browse last random quotes");

		// Try the random page
		$I->amOnRoute('home');
		$I->click('Random quotes', '.navbar');
		$this->assertPageOfQuotesContainsRequiredElements($I);
	}

	public function checkCommentsAndFavoritesAreSet(FunctionalTester $I)
	{
		$I->am('a member of Teen Quotes');
		$I->wantTo("view comments and favorites on last quotes");
		
		// Add to the user's favorites the first quote
		$I->addAFavoriteForUser($this->firstQuote->id, $this->user->id);

		// Go to the homepage
		$I->amOnRoute('home');

		// Assert that the number of comments is displayed
		$I->see($I->getNbComments().' comments', '.color-1');
		// Assert that the quote is marked as favorited
		$I->seeElement('.color-1 i.fa-heart');
		// Assert that the author of the quote is displayed
		$I->see($this->user->login, '.color-1 .link-author-profile');

		// I can view my profile when clicking on the author of a quote
		$I->click('.color-1 .link-author-profile');
		$I->seeCurrentRouteIs('users.show', $this->user->login);
	}

	public function checkAHiddenProfileCanNotBeClicked(FunctionalTester $I)
	{
		$I->am('a member of Teen Quotes');
		$I->wantTo("verify that I can't click on the author of a quote if its profile is hidden");

		$I->hideProfileForCurrentUser();

		// Go to the homepage
		$I->amOnRoute('home');
		// Check that the quote is not in my favorites
		$I->dontSeeElement('.color-1 a.link-author-profile');
	}

	private function assertPageOfQuotesContainsRequiredElements(FunctionalTester $I)
	{
		$I->seeNumberOfElements('.quote', $I->getNbQuotesPerPage());

		for ($i = 1; $i <= $I->getNbQuotesPerPage(); $i++) { 
			// Verify that we have got our quotes with different colors
			$I->seeElement('.color-'.$i);
			
			// All of them are not in my favorites
			$I->seeElement('.color-'.$i.' .favorite-action i.fa-heart-o');
			
			// All of them contain social media buttons
			$I->seeElement('.color-'.$i.' i.fa-facebook');
			$I->seeElement('.color-'.$i.' i.fa-twitter');
		}
		
		// I am on the first page
		$I->see('1', '#paginator-quotes ul li.active');
		
		// I can see that we have got our links to pages
		for ($i = 2; $i <= $I->getNbPagesToCreate(); $i++) { 
			$I->see($i, '#paginator-quotes li a');
		}

		// Go to the second page and check that the page
		// parameter has been set in the URL
		$I->click('2', '#paginator-quotes li a');
		$I->seeCurrentUrlMatches('#page=2#');
	}
}