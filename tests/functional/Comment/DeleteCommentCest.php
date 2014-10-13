<?php

use TeenQuotes\Quotes\Models\Quote;

class DeleteCommentCest {

	/**
	 * The number of comments to add of the first quote.
	 * The last comment is posted by the logged in user.
	 * @var integer
	 */
	private $nbComments = 5;

	/**
	 * The created quotes
	 * @var array
	 */
	private $quotes;

	/**
	 * The logged in user
	 * @var User
	 */
	private $user;
	
	public function _before(FunctionalTester $I)
	{
		$this->user = $I->logANewUser();
		$this->quotes = $I->createSomePublishedQuotes();

		// Insert some comments, the last one should have been written by the logged in user
		$I->insertInDatabase($this->nbComments - 1, 'TeenQuotes\Comments\Models\Comment', ['quote_id' => $this->quotes[0]->id]);
		$I->insertInDatabase(1, 'TeenQuotes\Comments\Models\Comment', ['quote_id' => $this->quotes[0]->id, 'user_id' => $this->user->id]);
	}

	public function deleteACommentOnAQuote(FunctionalTester $I)
	{
		$I->am('a member of Teen Quotes');
		$I->wantTo("delete a comment on a quote");

		// Go to the quote, verify that we have the right number of comments
		$I->amOnRoute('quotes.show', $this->quotes[0]->id);
		$I->seeNumberOfElements('.comment', $this->nbComments);

		// I see the delete button on my comment
		$I->seeNumberOfElements('i.fa-times', 1);
		$I->seeElement('.comment[data-id='.$this->nbComments.'] i.fa-times');
		
		// Send the Ajax request to delete my comment
		$I->sendAjaxDeleteRequest(URL::route('comments.destroy', $this->nbComments));
		
		// The comment should be removed thanks to jQuery
		// Go back to the page and verify that the comment has disappeared
		$I->amOnRoute('quotes.show', $this->quotes[0]->id);
		$I->seeNumberOfElements('.comment', $this->nbComments - 1);
		$I->dontSeeElement('i.fa-times');
		$I->assertEquals(0, Auth::user()->getTotalComments());
	}

	public function deleteACommentFromMyProfile(FunctionalTester $I)
	{
		$I->am('a member of Teen Quotes');
		$I->wantTo("delete a comment on a quote from my profile");

		$I->navigateToMyProfile();
		// Should have been auto redirected to the comments section since
		// we have no published quotes and no favorites
		$I->seeCurrentRouteIs('users.show', [$this->user->login, 'comments']);
		
		// I see the delete button on my comment
		$I->seeNumberOfElements('.comment', 1);
		$I->seeNumberOfElements('i.fa-times', 1);
		$I->seeElement('.comment[data-id='.$this->nbComments.'] i.fa-times');

		// Send the Ajax request to delete my comment
		$I->sendAjaxDeleteRequest(URL::route('comments.destroy', $this->nbComments));

		// "Refresh" the page
		$I->amOnRoute('users.show', [$this->user->login, 'comments']);
		// Since we have no longer comments, we should see the welcome tutorial
		$I->seeElement('#welcome-profile');

		// Run assertions about the number of comments
		$I->assertEquals(0, Auth::user()->getTotalComments());
		$I->dontSeeElement('.comment');
		$I->assertEquals($this->nbComments - 1, Quote::find($this->quotes[0]->id)->total_comments);
	}
}