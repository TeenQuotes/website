<?php

/*
 * This file is part of the Teen Quotes website.
 *
 * (c) Antoine Augusti <antoine.augusti@teen-quotes.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class FavoriteQuoteRepoCest
{
    /**
     * @var \TeenQuotes\Quotes\Repositories\FavoriteQuoteRepository
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
        $this->fav(1, $u);

        $I->assertTrue($this->repo->isFavoriteForUserAndQuote($u, 1));
        $I->assertFalse($this->repo->isFavoriteForUserAndQuote($u->id, 2));
    }

    public function testDeleteForUserAndQuote(IntegrationTester $I)
    {
        $u = $I->insertInDatabase(1, 'User');
        $I->insertInDatabase(2, 'Quote');
        $this->fav(1, $u);

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

        $this->fav(1, $u);
        $I->assertEquals([1], $this->repo->quotesFavoritesForUser($u->id));

        $this->fav(2, $u);
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

    public function testNbFavoritesForQuote(IntegrationTester $I)
    {
        $I->insertInDatabase(2, 'Quote');
        $u = $I->insertInDatabase(1, 'User');

        $I->assertEquals(0, $this->repo->nbFavoritesForQuote(1));

        $this->fav(1, $u);
        $I->assertEquals(1, $this->repo->nbFavoritesForQuote(1));
    }

    private function fav($quote_id, $user_id)
    {
        $this->repo->create($user_id, $quote_id);
    }
}
