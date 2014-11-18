<?php

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use TeenQuotes\Comments\Models\Comment;
use TeenQuotes\Quotes\Models\Quote;

class CommentsTest extends ApiTest {

	protected $requiredAttributes = ['id', 'content', 'quote_id', 'user_id', 'created_at'];
	protected $embedsRelation = ['user_small', 'quote'];
	protected $quoteId;

	protected function _before()
	{
		parent::_before();
		
		$this->unitTester->setController(App::make('TeenQuotes\Api\V1\Controllers\CommentsController'));

		$this->unitTester->setContentType('comments');

		// Create a quote and add some comments to it
		$q = $this->unitTester->insertInDatabase(1, 'Quote');
		$this->quoteId = $q['id'];
		$this->unitTester->insertInDatabase($this->unitTester->getNbRessources(), 'Comment', ['quote_id' => $this->quoteId]);
	}
	
	public function testIndexWithoutQuote()
	{
		$this->doNotEmbedsQuote();

		// Test with the middle page
		$this->unitTester->tryMiddlePage('index', $this->quoteId);

		// Test first page
		$this->unitTester->tryFirstPage('index', $this->quoteId);
	}

	public function testIndexWithQuote()
	{
		// Test with the middle page
		$this->activateEmbedsQuote();
		$this->unitTester->tryMiddlePage('index', $this->quoteId);

		// Test first page
		$this->activateEmbedsQuote();
		$this->unitTester->tryFirstPage('index', $this->quoteId);
	}

	/**
	 * @expectedException        TeenQuotes\Exceptions\ApiNotFoundException
	 * @expectedExceptionMessage comments
	 */
	public function testIndexNotFound()
	{
		// Test not found
		$this->unitTester->tryPaginatedContentNotFound($this->quoteId);
	}

	public function testShowNotFound()
	{
		// Not found comment
		$this->unitTester->tryShowNotFound($this->unitTester->getIdNonExistingRessource())
			->withStatusMessage('comment_not_found')
			->withErrorMessage('The comment #'.$this->unitTester->getIdNonExistingRessource().' was not found.');
	}

	public function testShowFoundWithoutQuote()
	{
		$this->doNotEmbedsQuote();
		$this->unitTester->tryShowFound($this->quoteId);
	}

	public function testShowFoundWithQuote()
	{		
		$this->activateEmbedsQuote();
		$this->unitTester->tryShowFound($this->quoteId);
	}

	/**
	 * @expectedException Laracasts\Validation\FormValidationException
	 * @expectedExceptionMessage The content field is required.
	 */
	public function testStoreEmptyContent()
	{
		// Empty content
		$this->unitTester->addInputReplace(['content' => '']);
		
		$this->assertStoreWithWrongContent();
	}

	/**
	 * @expectedException Laracasts\Validation\FormValidationException
	 * @expectedExceptionMessage The content must be at least 10 characters.
	 */
	public function testStoreTooSmallContent()
	{
		// Too small content
		$this->unitTester->addInputReplace(['content' => $this->unitTester->generateString(9)]);
		
		$this->assertStoreWithWrongContent();
	}

	/**
	 * @expectedException Laracasts\Validation\FormValidationException
	 * @expectedExceptionMessage The content may not be greater than 500 characters.
	 */
	public function testStoreTooLongContent()
	{
		// Too long content
		$this->unitTester->addInputReplace(['content' => $this->unitTester->generateString(501)]);
		
		$this->assertStoreWithWrongContent();
	}

	/**
	 * @expectedException Laracasts\Validation\FormValidationException
	 * @expectedExceptionMessage The selected quote id was not found.
	 */
	public function testStoreQuoteIdNotFound()
	{
		$this->unitTester->addInputReplace(['content' => $this->unitTester->generateString(100)]);

		$this->store($this->unitTester->getIdNonExistingRessource());
	}

	public function testStoreSuccess()
	{
		$q = Quote::find($this->quoteId);

		// Check number of comments in cache
		$this->assertEquals($this->unitTester->getNbRessources(), $q->total_comments);
		$this->assertEquals($this->unitTester->getNbRessources(), Cache::get(Quote::$cacheNameNbComments.$q->id));

		$oldNbComments = $q->total_comments;

		// Store in a new comment
		$this->unitTester->logUserWithId(1);
		$this->unitTester->addInputReplace([
			'content' => $this->unitTester->generateString(150),
		]);
		
		$this->store($q->id)
			->apiHelper->assertStatusCodeIs(Response::HTTP_CREATED)
			->assertResponseHasRequiredAttributes();

		// Verify that the cache has been incremented
		$this->assertEquals($oldNbComments + 1, $q->total_comments);
		$this->assertEquals($oldNbComments + 1, Cache::get(Quote::$cacheNameNbComments.$q->id));
	}

	public function testDestroyCommentNotFound()
	{
		$this->unitTester->logUserWithId(1);

		$this->destroy($this->unitTester->getIdNonExistingRessource())
			->apiHelper->assertStatusCodeIs(Response::HTTP_NOT_FOUND)
			->withStatusMessage('comment_not_found')
			->withErrorMessage('The comment #'.$this->unitTester->getIdNonExistingRessource().' was not found.');
	}

	public function testDestroyCommentNotOwned()
	{
		// Create a comment not owned by the logged in user
		$u = $this->unitTester->insertInDatabase(1, 'Quote', ['id' => 500]);
		$c = $this->unitTester->insertInDatabase(1, 'Comment', ['user_id' => $u['id']]);

		$idUserLoggedIn = 1;
		$this->unitTester->logUserWithId($idUserLoggedIn);

		$this->destroy($c['id'])
			->apiHelper->assertStatusCodeIs(Response::HTTP_BAD_REQUEST)
			->withStatusMessage('comment_not_self')
			->withErrorMessage('The comment #'.$c['id'].' was not posted by user #'.$idUserLoggedIn.'.');
	}

	public function testDestroySuccess()
	{
		$c = Comment::first();

		$this->unitTester->logUserWithId($c->user_id);
		
		$this->destroy($c->id)
			->apiHelper->assertStatusCodeIs(Response::HTTP_OK)
			->withStatusMessage('comment_deleted')
			->withSuccessMessage('The comment #'.$c->id.' was deleted.');

		$this->assertEmpty(Comment::find($c->id));
	}

	public function testUpdateCommentNotFound()
	{
		$this->unitTester->logUserWithId(1);

		$this->update($this->unitTester->getIdNonExistingRessource())
			->apiHelper->assertStatusCodeIs(Response::HTTP_NOT_FOUND)
			->withStatusMessage('comment_not_found')
			->withErrorMessage('The comment #'.$this->unitTester->getIdNonExistingRessource().' was not found.');
	}

	public function testUpdateCommentNotOwned()
	{
		// Create a comment not owned by the logged in user
		$u = $this->unitTester->insertInDatabase(1, 'Quote', ['id' => 500]);
		$c = $this->unitTester->insertInDatabase(1, 'Comment', ['user_id' => $u['id']]);

		$idUserLoggedIn = 1;
		$this->unitTester->logUserWithId($idUserLoggedIn);

		$this->update($c['id'])
			->apiHelper->assertStatusCodeIs(Response::HTTP_BAD_REQUEST)
			->withStatusMessage('comment_not_self')
			->withErrorMessage('The comment #'.$c['id'].' was not posted by user #'.$idUserLoggedIn.'.');
	}

	/**
	 * @expectedException Laracasts\Validation\FormValidationException
	 * @expectedExceptionMessage The content must be at least 10 characters.
	 */
	public function testUpdateTooSmallContent()
	{
		// Too small content
		$this->unitTester->addInputReplace(['content' => $this->unitTester->generateString(9)]);
		
		$this->assertUpdateWithWrongContent();
	}

	/**
	 * @expectedException Laracasts\Validation\FormValidationException
	 * @expectedExceptionMessage The content may not be greater than 500 characters.
	 */
	public function testUpdateTooLongContent()
	{
		// Too long content
		$this->unitTester->addInputReplace(['content' => $this->unitTester->generateString(501)]);
		
		$this->assertUpdateWithWrongContent();
	}

	/**
	 * @expectedException Laracasts\Validation\FormValidationException
	 * @expectedExceptionMessage The content field is required.
	 */
	public function testUpdateRequiredContent()
	{
		// No content
		$this->unitTester->addInputReplace(['content' => '']);
		
		$this->assertUpdateWithWrongContent();
	}

	public function testUpdateSuccess()
	{
		$c = Comment::first();

		$this->unitTester->logUserWithId($c->user_id);

		$this->unitTester->addInputReplace([
			'content' => $this->unitTester->generateString(150),
		]);
		
		$this->update($c->id)
			->apiHelper->assertStatusCodeIs(Response::HTTP_OK)
			->withStatusMessage('comment_updated')
			->withSuccessMessage('The comment #'.$c->id.' was updated.');
	}

	public function testIndexForUser()
	{
		$u = $this->unitTester->insertInDatabase(1, 'User');
		$this->unitTester->insertInDatabase($this->unitTester->getNbRessources(), 'Comment', ['user_id' => $u->id]);
		
		$this->activateEmbedsQuote();

		// Test first page
		$this->unitTester->tryFirstPage('getCommentsForUser', $u->id);

		// Test with the middle page
		$this->unitTester->tryMiddlePage('getCommentsForUser', $u->id);
	}

	public function testIndexForUserNotFound()
	{
		$this->unitTester->doRequest('getCommentsForUser', 100);
		
		$this->unitTester->assertStatusCodeIs(Response::HTTP_BAD_REQUEST)
			->withStatusMessage('user_not_found')
			->withErrorMessage('The user #100 was not found.');
	}

	/**
	 * @expectedException        TeenQuotes\Exceptions\ApiNotFoundException
	 * @expectedExceptionMessage comments
	 */
	public function testIndexForUserNoComments()
	{
		$u = $this->unitTester->insertInDatabase(1, 'User');
		
		// Try with a user with no comments
		$this->unitTester->tryFirstPage('getCommentsForUser', $u->id);
	}

	private function assertStoreWithWrongContent()
	{
		$this->unitTester->logUserWithId(1);
		
		$this->store($this->quoteId);
	}

	private function assertUpdateWithWrongContent()
	{
		$c = Comment::first();
		$this->unitTester->logUserWithId($c->user_id);
		
		$this->store($c->id);
	}

	private function store($id)
	{
		$this->unitTester->tryStore('store', $id);

		return $this;
	}

	private function destroy($id)
	{
		$this->unitTester->doRequest('destroy', $id);

		return $this;
	}

	private function update($id)
	{
		$this->unitTester->doRequest('update', $id);

		return $this;
	}

	private function doNotEmbedsQuote()
	{
		$this->unitTester->setEmbedsRelation(['user_small']);
	}

	private function activateEmbedsQuote()
	{
		$this->unitTester->setEmbedsRelation(['user_small', 'quote']);
		$this->unitTester->addInputReplace(['quote' => true]);
	}
}