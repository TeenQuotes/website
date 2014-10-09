<?php

class AddCommentCest {

	/**
	 * The number of comments to add of the first quote
	 * @var integer
	 */
	private $nbComments = 5;

	/**
	 * The created quotes
	 * @var array
	 */
	private $quotes;
	
	public function _before(FunctionalTester $I)
	{
		$I->logANewUser();
		$this->quotes = $I->createSomePublishedQuotes();
		$I->insertInDatabase($this->nbComments, 'Comment', ['quote_id' => $this->quotes[0]->id]);
	}

	public function postANewCommentOnAQuote(FunctionalTester $I)
	{
		$I->am('a member of Teen Quotes');
		$I->wantTo("add a comment on a quote");

		// Go to the quote, verify that we have the right number of comments
		$I->amOnRoute('quotes.show', $this->quotes[0]->id);
		$I->seeNumberOfElements('.comment', $this->nbComments);

		// Post a new comment
		$txtToAdd = Str::random(100);
		$I->fillAddCommentForm($txtToAdd);

		// Assert that the comment has been added to this quote
		$nbCommentsExpected = $this->nbComments + 1;
		$I->amOnRoute('quotes.show', $this->quotes[0]->id);
		$I->seeSuccessFlashMessage('Your comment has been added successfully!');
		$I->seeNumberOfElements('.comment', $nbCommentsExpected);
		$I->see($txtToAdd, '.comment[data-id='.($nbCommentsExpected).']');
		// We have got a link to the profile of the comment's author
		$I->see(Auth::user()->login, '.comment[data-id='.$nbCommentsExpected.'] a.link-author-name');
		$I->assertEquals(1, Auth::user()->getTotalComments());
		$I->assertEquals($nbCommentsExpected, Quote::find($this->quotes[0]->id)->total_comments);
	}

	public function postATooShortCommentOnAQuote(FunctionalTester $I)
	{
		$I->am('a member of Teen Quotes');
		$I->wantTo("add a comment too short on a quote");

		// Go to the quote, verify that we have the right number of comments
		$I->amOnRoute('quotes.show', $this->quotes[0]->id);
		$I->seeNumberOfElements('.comment', $this->nbComments);

		// Try to post a new comment
		$txtToAdd = Str::random(9);
		$I->fillAddCommentForm($txtToAdd);

		// Assert that we have got an error
		$I->amOnRoute('quotes.show', $this->quotes[0]->id);
		$I->seeFormError('The content must be at least 10 characters.');
		
		// The comment was not posted
		$I->seeNumberOfElements('.comment', $this->nbComments);
		$I->assertEquals($this->nbComments, Quote::find($this->quotes[0]->id)->total_comments);
	}

	public function postATooLongCommentOnAQuote(FunctionalTester $I)
	{
		$I->am('a member of Teen Quotes');
		$I->wantTo("add a comment too long on a quote");

		// Go to the quote, verify that we have the right number of comments
		$I->amOnRoute('quotes.show', $this->quotes[0]->id);
		$I->seeNumberOfElements('.comment', $this->nbComments);

		// Try to post a new comment
		$txtToAdd = Str::random(501);
		$I->fillAddCommentForm($txtToAdd);

		// Assert that we have got an error
		$I->amOnRoute('quotes.show', $this->quotes[0]->id);
		$I->seeFormError('The content may not be greater than 500 characters.');
		
		// The comment was not posted
		$I->seeNumberOfElements('.comment', $this->nbComments);
		$I->assertEquals($this->nbComments, Quote::find($this->quotes[0]->id)->total_comments);
	}

	public function postACommentIsRequired(FunctionalTester $I)
	{
		$I->am('a member of Teen Quotes');
		$I->wantTo("leave an empty comment on a quote");

		// Go to the quote, verify that we have the right number of comments
		$I->amOnRoute('quotes.show', $this->quotes[0]->id);
		$I->seeNumberOfElements('.comment', $this->nbComments);

		// Try to post a new comment
		$I->fillAddCommentForm('');

		// Assert that we have got an error
		$I->amOnRoute('quotes.show', $this->quotes[0]->id);
		$I->seeFormError('The content field is required.');
		
		// The comment was not posted
		$I->seeNumberOfElements('.comment', $this->nbComments);
		$I->assertEquals($this->nbComments, Quote::find($this->quotes[0]->id)->total_comments);
	}
}