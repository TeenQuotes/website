<?php

class FavoriteQuoteRepoCest {

	/**
	 * @var TeenQuotes\Quotes\Repositories\FavoriteQuoteRepository
	 */
	private $repo;

	public function _before()
	{
		$this->repo = App::make('TeenQuotes\Quotes\Repositories\FavoriteQuoteRepository');
	}

	public function testIsFavoriteForUserAndQuote(IntegrationTester $I)
	{
		$u = $I->insertInDatabase(1, 'User');
		$I->insertInDatabase(2, 'Quote');
		$this->fav($I, 1, $u->id);

		$I->assertTrue($this->repo->isFavoriteForUserAndQuote($u, 1));
		$I->assertFalse($this->repo->isFavoriteForUserAndQuote($u->id, 2));
	}

	public function testDeleteForUserAndQuote(IntegrationTester $I)
	{
		$u = $I->insertInDatabase(1, 'User');
		$I->insertInDatabase(2, 'Quote');
		$this->fav($I, 1, $u->id);

		$this->repo->deleteForUserAndQuote($u, 1);

		$I->assertFalse($this->repo->isFavoriteForUserAndQuote($u, 1));
	}

	public function testNbFavoritesForQuotes(IntegrationTester $I)
	{
		$I->insertInDatabase(2, 'Quote');
		$I->insertInDatabase(2, 'FavoriteQuote', ['quote_id' => 1]);
		$I->insertInDatabase(3, 'FavoriteQuote', ['quote_id' => 2]);

		$I->assertEquals(2, $this->repo->nbFavoritesForQuotes([1]));
		$I->assertEquals(5, $this->repo->nbFavoritesForQuotes([1, 2]));
	}

	public function quotesFavoritesForUser(IntegrationTester $I)
	{
		$u = $I->insertInDatabase(1, 'User');
		$I->insertInDatabase(2, 'Quote');

		$I->assertEquals([], $this->repo->quotesFavoritesForUser($u));

		$this->fav($I, 1, $u->id);
		$I->assertEquals([1], $this->repo->quotesFavoritesForUser($u->id));

		$this->fav($I, 2, $u->id);
		$out = $this->repo->quotesFavoritesForUser($u->id);
		sort($out);
		$I->assertEquals([1, 2], $out);

		$this->repo->deleteForUserAndQuote($u, 2);
		$I->assertEquals([1], $this->repo->quotesFavoritesForUser($u->id));
	}

	public function testCreate(IntegrationTester $I)
	{
		$u = $I->insertInDatabase(1, 'User');
		$I->insertInDatabase(1, 'Quote');

		$I->assertFalse($this->repo->isFavoriteForUserAndQuote(1, 1));

		$this->repo->create($u, 1);
		$I->assertTrue($this->repo->isFavoriteForUserAndQuote(1, 1));
	}

	public function testGetTopQuotes(IntegrationTester $I)
	{
		$I->insertInDatabase(2, 'Quote');
		$I->insertInDatabase(3, 'FavoriteQuote', ['quote_id' => 2]);
		$I->insertInDatabase(2, 'FavoriteQuote', ['quote_id' => 1]);

		$I->assertEquals([2], $this->repo->getTopQuotes(1, 1));
		$I->assertEquals([2, 1], $this->repo->getTopQuotes(1, 3));
	}

	private function fav(IntegrationTester $I, $quote_id, $user_id)
	{
		$I->insertInDatabase(1, 'FavoriteQuote', compact('quote_id', 'user_id'));
	}
}