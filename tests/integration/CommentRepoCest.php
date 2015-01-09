<?php

class CommentRepoCest {

	/**
	 * @var TeenQuotes\Comments\Repositories\CommentRepository
	 */
	private $repo;

	public function _before()
	{
		$this->repo = App::make('TeenQuotes\Comments\Repositories\CommentRepository');
	}

	public function testFindById(IntegrationTester $I)
	{
		$c = $I->insertInDatabase(1, 'Comment');

		$comment = $this->repo->findById($c->id);

		$I->assertEquals($c->content, $comment->content);
		$I->assertEquals($c->user_id, $comment->user_id);
	}

	public function testFindByIdWithQuote(IntegrationTester $I)
	{
		$c = $I->insertInDatabase(1, 'Comment');

		$comment = $this->repo->findById($c->id);

		$I->assertTrue($c->quote instanceof TeenQuotes\Quotes\Models\Quote);
	}

	public function testIndexForQuote(IntegrationTester $I)
	{
		$I->insertInDatabase(1, 'Comment');
		$q = $I->insertInDatabase(1, 'Quote');
		$I->insertInDatabase(2, 'Comment', ['quote_id' => $q->id]);

		$comments = $this->repo->indexForQuote($q->id, 1, 5);

		$I->assertIsCollection($comments);
		$I->assertEquals(2, count($comments));
	}

	public function testIndexForQuoteWithQuote(IntegrationTester $I)
	{
		$I->insertInDatabase(1, 'Comment');
		$q = $I->insertInDatabase(1, 'Quote');
		$I->insertInDatabase(2, 'Comment', ['quote_id' => $q->id]);

		$comments = $this->repo->indexForQuote($q->id, 1, 5);

		$I->assertIsCollection($comments);
		$I->assertTrue($comments->first()->quote instanceof TeenQuotes\Quotes\Models\Quote);
	}

	public function findForUser(IntegrationTester $I)
	{
		$I->insertInDatabase(1, 'Comment');
		$u = $I->insertInDatabase(1, 'User');
		$I->insertInDatabase(2, 'Comment', ['user_id' => $u->id]);

		$comments = $this->repo->findForUser($u, 1, 1);
		$I->assertIsCollection($comments);
		$I->assertEquals(1, count($comments));

		$comments = $this->repo->findForUser($u, 1, 3);
		$I->assertEquals(2, count($comments));
	}

	public function testCountForUser(IntegrationTester $I)
	{
		$I->insertInDatabase(1, 'Comment');
		$u = $I->insertInDatabase(1, 'User');
		$I->insertInDatabase(2, 'Comment', ['user_id' => $u->id]);

		$I->assertEquals(2, $this->repo->countForUser($u));
	}

	public function testCreate(IntegrationTester $I)
	{
		$q = $I->insertInDatabase(1, 'Quote');
		$u = $I->insertInDatabase(1, 'User');

		$this->repo->create($q, $u, "Hello World");

		$I->assertEquals(1, $this->repo->countForUser($u));
	}

	public function testUpdate(IntegrationTester $I)
	{
		$c = $I->insertInDatabase(1, 'Comment');
		$newContent = "Foobar";

		$this->repo->update($c, $newContent);

		$comment = $this->repo->findById($c->id);
		$I->assertEquals($comment->content, $newContent);
	}

	public function testDelete(IntegrationTester $I)
	{
		$c = $I->insertInDatabase(1, 'Comment');

		$this->repo->delete($c->id);

		$I->assertNull($this->repo->findById($c->id));
	}

	public function testGetTopQuotes(IntegrationTester $I)
	{
		$I->assertEquals([], $this->repo->getTopQuotes(1, 10));

		$I->insertInDatabase(2, 'Quote');
		$I->insertInDatabase(2, 'Comment', ['quote_id' => 2]);
		$I->insertInDatabase(1, 'Comment', ['quote_id' => 1]);

		$I->assertEquals([2, 1], $this->repo->getTopQuotes(1, 10));
	}
}