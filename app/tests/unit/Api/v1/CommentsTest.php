<?php

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Laracasts\TestDummy\Factory;
use TeenQuotes\Comments\Models\Comment;
use TeenQuotes\Quotes\Models\Quote;

class CommentsTest extends ApiTest {

	protected $requiredAttributes = ['id', 'content', 'quote_id', 'user_id', 'created_at'];
	protected $embedsRelation = ['user_small', 'quote'];
	protected $contentType = 'comments';
	protected $quoteId;

	public function setUp()
	{
		parent::setUp();

		$this->controller = App::make('TeenQuotes\Api\V1\Controllers\CommentsController');
		
		// Create a quote and add some comments to it
		$q = Factory::create('TeenQuotes\Quotes\Models\Quote');
		$this->quoteId = $q['id'];
		Factory::times($this->nbRessources)->create('TeenQuotes\Comments\Models\Comment', ['quote_id' => $this->quoteId]);
	}
	
	public function testIndexWithoutQuote()
	{
		$this->doNotEmbedsQuote();

		// Test with the middle page
		$this->tryMiddlePage('index', $this->quoteId);

		// Test first page
		$this->tryFirstPage('index', $this->quoteId);
	}

	public function testIndexWithQuote()
	{
		// Test with the middle page
		$this->activateEmbedsQuote();
		$this->tryMiddlePage('index', $this->quoteId);

		// Test first page
		$this->activateEmbedsQuote();
		$this->tryFirstPage('index', $this->quoteId);
	}

	public function testIndexNotFound()
	{
		// Test not found
		$this->tryPaginatedContentNotFound($this->quoteId)
			->withStatusMessage(404)
			->withErrorMessage('No comments have been found.');
	}

	public function testShowNotFound()
	{
		// Not found comment
		$this->tryShowNotFound($this->getIdNonExistingRessource())
			->withStatusMessage('comment_not_found')
			->withErrorMessage('The comment #'.$this->getIdNonExistingRessource().' was not found');
	}

	public function testShowFoundWithoutQuote()
	{
		$this->doNotEmbedsQuote();
		$this->tryShowFound($this->quoteId);
	}

	public function testShowFoundWithQuote()
	{		
		$this->activateEmbedsQuote();
		$this->tryShowFound($this->quoteId);
	}

	public function testStoreEmptyContent()
	{
		// Empty content
		$this->addInputReplace(['content' => '']);
		
		$this->assertStoreWithWrongContent()
			->withErrorMessage('The content field is required.');
	}

	public function testStoreTooSmallContent()
	{
		// Too small content
		$this->addInputReplace(['content' => $this->generateString(9)]);
		
		$this->assertStoreWithWrongContent()
			->withErrorMessage('The content must be at least 10 characters.');
	}

	public function testStoreTooLongContent()
	{
		// Too long content
		$this->addInputReplace(['content' => $this->generateString(501)]);
		
		$this->assertStoreWithWrongContent()
			->withErrorMessage('The content may not be greater than 500 characters.');
	}

	public function testStoreQuoteIdNotFound()
	{
		$this->addInputReplace([
			'content' => $this->generateString(100),
		]);

		$this->store($this->getIdNonExistingRessource())
			->assertStatusCodeIs(Response::HTTP_BAD_REQUEST)
			->withStatusMessage('wrong_quote_id')
			->withErrorMessage('The selected quote id was not found.');
	}

	public function testStoreSuccess()
	{
		$q = Quote::find($this->quoteId);

		// Check number of comments in cache
		$this->assertEquals($this->nbRessources, $q->total_comments);
		$this->assertEquals($this->nbRessources, Cache::get(Quote::$cacheNameNbComments.$q->id));

		$oldNbComments = $q->total_comments;

		// Store in a new comment
		$this->logUserWithId(1);
		$this->addInputReplace([
			'content' => $this->generateString(150),
		]);
		
		$this->store($q->id)
			->assertStatusCodeIs(Response::HTTP_CREATED)
			->assertResponseHasRequiredAttributes();

		// Verify that the cache has been incremented
		$this->assertEquals($oldNbComments + 1, $q->total_comments);
		$this->assertEquals($oldNbComments + 1, Cache::get(Quote::$cacheNameNbComments.$q->id));
	}

	public function testDestroyCommentNotFound()
	{
		$this->logUserWithId(1);

		$this->destroy($this->getIdNonExistingRessource())
			->assertStatusCodeIs(Response::HTTP_NOT_FOUND)
			->withStatusMessage('comment_not_found')
			->withErrorMessage('The comment #'.$this->getIdNonExistingRessource().' was not found.');
	}

	public function testDestroyCommentNotOwned()
	{
		// Create a comment not owned by the logged in user
		$u = Factory::create('User', ['id' => 500]);
		$c = Factory::create('TeenQuotes\Comments\Models\Comment', ['user_id' => $u['id']]);

		$idUserLoggedIn = 1;
		$this->logUserWithId($idUserLoggedIn);

		$this->destroy($c['id'])
			->assertStatusCodeIs(Response::HTTP_BAD_REQUEST)
			->withStatusMessage('comment_not_self')
			->withErrorMessage('The comment #'.$c['id'].' was not posted by user #'.$idUserLoggedIn.'.');
	}

	public function testDestroySuccess()
	{
		$c = Comment::first();

		$this->logUserWithId($c->user_id);
		
		$this->destroy($c->id)
			->assertStatusCodeIs(Response::HTTP_OK)
			->withStatusMessage('comment_deleted')
			->withSuccessMessage('The comment #'.$c->id.' was deleted.');

		$this->assertEmpty(Comment::find($c->id));
	}

	private function destroy($id)
	{
		$this->doRequest('destroy', $id);

		return $this;
	}

	private function assertStoreWithWrongContent()
	{
		$this->logUserWithId(1);
		
		$this->store($this->quoteId)
			->assertStatusCodeIs(Response::HTTP_BAD_REQUEST)
			->withStatusMessage('wrong_content');

		return $this;
	}

	private function store($id)
	{
		return $this->tryStore('store', $id);
	}

	private function doNotEmbedsQuote()
	{
		$this->embedsRelation = ['user_small'];
	}

	private function activateEmbedsQuote()
	{
		$this->embedsRelation = ['user_small', 'quote'];
		$this->addInputReplace(['quote' => true]);
	}
}