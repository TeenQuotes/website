<?php

class IndexStoriesCest {
	
	/**
	 * The logged in user
	 * @var User
	 */
	private $user;

	public function _before(FunctionalTester $I)
	{		
		// Create some published quotes and some stories
		$I->createSomePublishedQuotes();
		$I->createSomeStories();

		// Create a new user
		$this->user = $I->logANewUser();
		$I->navigateToTheStoryPage();
	}

	public function browseLastStories(FunctionalTester $I)
	{
		$I->am('a member of Teen Quotes');
		$I->wantTo("browse last stories");

		$this->assertPageOfQuotesContainsRequiredElements($I);
	}

	private function assertPageOfQuotesContainsRequiredElements(FunctionalTester $I)
	{
		$I->seeNumberOfElements('.story', $I->getNbStoriesPerPage());

		// Start with the ID of the story on the first page
		$id = 1;

		for ($i = 1; $i <= $I->getNbStoriesPerPage(); $i++) { 

			// Avatar of the author of the story
			$I->seeElement('.story[data-id="'.$id.'"] img.avatar');
			// Date of the story
			$I->seeElement('.story[data-id="'.$id.'"] .story-date');
			// Link to the single story
			$I->seeElement('.story[data-id="'.$id.'"] .story-id a');

			$id++;
		}
		
		// I am on the first page
		$I->see('1', 'ul.pagination li.active');
		
		// I can see that we have got our links to pages
		for ($i = 2; $i <= $I->getNbPagesToCreate(); $i++) { 
			$I->see($i, 'ul.pagination li a');
		}

		// Go to the second page
		$I->click('2', 'ul.pagination li a');
		
		// Check that the page parameter has been set in the URL
		// and the paginator has been updated
		$I->seeCurrentUrlMatches('#page=2#');
		$I->see('2', 'ul.pagination li.active');
	}
}