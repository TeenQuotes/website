<?php

class IndexQuoteCest {
	
	public function _before(FunctionalTester $I)
	{
		$I->logANewUser();
		$I->createSomePublishedQuotes(['nb_quotes' => $this->getNbQuotesPerPage() * 3]);
	}

	public function browseLastQuotes(FunctionalTester $I)
	{
		$I->am('a member of Teen Quotes');
		$I->wantTo("browse last quotes");

		$I->amOnRoute('home');

		// Verify that we have got 10 quotes with different colors
		// All of them are not in my favorites
		// All of them contain social media buttons
		for ($i = 1; $i <= $this->getNbQuotesPerPage(); $i++) { 
			$I->seeElement('.color-'.$i);
			$I->seeElement('.color-'.$i.' .favorite-action i.fa-heart-o');
			$I->seeElement('.color-'.$i.' i.fa-facebook');
			$I->seeElement('.color-'.$i.' i.fa-twitter');
		}
		
		// I am on the first page
		$I->see('1', '#paginator-quotes ul li.active');
		
		// I can see that we have got 3 pages
		for ($i = 2; $i <= 3; $i++) { 
			$I->see($i, '#paginator-quotes li a');
		}

		// Go to the second page and check that the page
		// parameter has been set in the URL
		$I->click('2', '#paginator-quotes li a');
		$I->seeCurrentUrlMatches('#page=2#');
	}

	public function checkCommentsAndFavoritesAreSet(FunctionalTester $I)
	{
		$I->am('a member of Teen Quotes');
		$I->wantTo("view comments and favorites on last quotes");

		// Create a new user, a fresh published quote and some comments to it
		// Add to the user's favorites this quote
		$u = $I->logANewUser();
		$nbComments = 5;
		$q = $I->insertInDatabase(1, 'Quote', ['created_at' => Carbon::now()->addMonth(), 'user_id' => $u->id]);
		$I->insertInDatabase($nbComments, 'Comment', ['quote_id' => $q->id]);
		$I->addAFavoriteForUser($q->id, $u->id);

		$I->amOnRoute('home');
		// Assert that the number of comments is displayed
		$I->see($nbComments.' comments', '.color-1');
		// Assert that the quote is marked as favorited
		$I->seeElement('.color-1 i.fa-heart');
		// Assert that the author of the quote is displayed
		$I->see($u->login, '.color-1 .link-author-profile');
	}

	private function getNbQuotesPerPage()
	{
		return Config::get('app.quotes.nbQuotesPerPage');
	}
}