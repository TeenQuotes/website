<?php

use TeenQuotes\Quotes\Models\Quote;

class EditCommentCest {

	/**
	 * The number of comments to add to the first quote
	 * @var integer
	 */
	private $nbComments = 5;

	/**
	 * The first quote that contains comments
	 * @var integer
	 */
	private $firstQuoteId;

	/**
	 * My comment
	 * @var StdClass
	 */
	private $myComment;
	
	public function _before(FunctionalTester $I)
	{
		$u = $I->logANewUser();
		
		// Create some quotes and add some comments to the first quote
		$this->firstQuoteId = array_values($I->createSomePublishedQuotes())[0]->id;
		$I->insertInDatabase($this->nbComments, 'Comment', ['quote_id' => $this->firstQuoteId]);
		
		// Create a comment posted by the user
		$this->myComment = $I->insertInDatabase(1, 'Comment', ['quote_id' => $this->firstQuoteId, 'user_id' => $u->id]);
	}

	public function editACommentFromAQuote(FunctionalTester $I)
	{
		$I->am('a member of Teen Quotes');
		$I->wantTo("edit one of my comments from a quote page");

		$newContent = Str::random(15);

		// Go to the quote, verify that I can edit my comment
		$I->amOnRoute('quotes.show', $this->firstQuoteId);
		$I->seeNumberOfElements('a.edit-comment', 1);
		$I->seeElement('.comment[data-id='.$this->myComment->id.'] a.edit-comment');
		
		// Go to the edit comment page and fill the form
		$this->goToTheEditFormForMyComment($I);
		$I->fillEditCommentForm($newContent);

		// I am redirected back to the quote page
		$I->seeCurrentRouteIs('quotes.show', $this->firstQuoteId);
		// And the comment has got the new content
		$I->see($newContent, '.comment[data-id='.$this->myComment->id.']');
	}

	public function editACommentFromAQuoteWithTooSmallContent(FunctionalTester $I)
	{
		$I->am('a member of Teen Quotes');
		$I->wantTo("edit a comment with a too small content");

		$newContent = Str::random(9);
		$this->fillEditFormAndSeeFormError($I, $newContent, "The content must be at least 10 characters.");
	}

	public function editACommentFromAQuoteWithTooLongContent(FunctionalTester $I)
	{
		$I->am('a member of Teen Quotes');
		$I->wantTo("edit a comment with a too long content");

		$newContent = Str::random(501);
		$this->fillEditFormAndSeeFormError($I, $newContent, "The content may not be greater than 500 characters.");
	}

	private function fillEditFormAndSeeFormError(FunctionalTester $I, $toFill, $expectedError)
	{		
		$I->amOnRoute('quotes.show', $this->firstQuoteId);
		
		// Go to the edit comment page and fill the form
		$this->goToTheEditFormForMyComment($I);
		$I->fillEditCommentForm($toFill);

		// I am still on the form
		$I->seeCurrentRouteIs('comments.edit', $this->myComment->id);
		// And we have got a form error
		$I->seeFormError($expectedError);
	}

	private function goToTheEditFormForMyComment(FunctionalTester $I)
	{
		$I->click('.comment[data-id='.$this->myComment->id.'] a.edit-comment');
		$I->seeCurrentRouteIs('comments.edit', $this->myComment->id);
		$I->see('Update my comment', 'h2');
		$I->seeInTitle('Update my comment');
	}
}