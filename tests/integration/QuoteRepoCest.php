<?php

use TeenQuotes\Quotes\Models\Quote;

class QuoteRepoCest {

	/**
	 * @var TeenQuotes\Quotes\Repositories\QuoteRepository
	 */
	private $repo;

	/**
	 * @var \TeenQuotes\Tags\Repositories\TagRepository
	 */
	private $tagRepo;

	public function _before()
	{
		$this->repo = App::make('TeenQuotes\Quotes\Repositories\QuoteRepository');
		$this->tagRepo = App::make('TeenQuotes\Tags\Repositories\TagRepository');
	}

	public function testRetrieveLastWaitingQuotes(IntegrationTester $I)
	{
		$I->insertInDatabase(3, 'Quote', ['approved' => Quote::PUBLISHED]);
		$I->insertInDatabase(4, 'Quote', ['approved' => Quote::WAITING]);

		$quotes = $this->repo->lastWaitingQuotes();

		$I->assertIsCollection($quotes);
		$I->assertEquals(4, count($quotes));
	}

	public function testNbPending(IntegrationTester $I)
	{
		$I->insertInDatabase(3, 'Quote', ['approved' => Quote::PUBLISHED]);
		$I->insertInDatabase(4, 'Quote', ['approved' => Quote::PENDING]);

		$I->assertEquals(4, $this->repo->nbPending());
	}

	public function testGetById(IntegrationTester $I)
	{
		$content = 'Testing you know';
		$I->insertInDatabase(1, 'Quote', ['content' => $content]);

		$quote = $this->repo->getById(1);
		$I->assertEquals($content, $quote->content);
	}

	public function testGetByIdWithUser(IntegrationTester $I)
	{
		$user = $I->insertInDatabase(1, 'User');
		$I->insertInDatabase(1, 'Quote', ['user_id' => $user->id]);

		$quote = $this->repo->getByIdWithUser(1);

		$I->assertEquals($user->id, $quote->user->id);
		$I->assertEquals($user->login, $quote->user->login);
	}

	public function testWaitingById(IntegrationTester $I)
	{
		$I->insertInDatabase(3, 'Quote', ['approved' => Quote::PUBLISHED]);
		$q = $I->insertInDatabase(1, 'Quote', ['approved' => Quote::WAITING]);

		$quote = $this->repo->waitingById($q->id);

		$I->assertEquals($q->content, $quote->content);
	}

	public function testShowQuote(IntegrationTester $I)
	{
		$quotes = $I->insertInDatabase(3, 'Quote', ['approved' => Quote::PUBLISHED]);

		$quote = $this->repo->showQuote(2);

		$I->assertEquals($quote->id, $quotes[1]->id);
	}

	public function testCountQuotesByApprovedForUser(IntegrationTester $I)
	{
		$user = $I->insertInDatabase(1, 'User');
		$I->insertInDatabase(3, 'Quote', ['approved' => Quote::WAITING, 'user_id' => $user->id]);
		$I->insertInDatabase(2, 'Quote', ['approved' => Quote::PUBLISHED, 'user_id' => $user->id]);

		$I->assertEquals(3, $this->repo->countQuotesByApprovedForUser('waiting', $user));
		$I->assertEquals(2, $this->repo->countQuotesByApprovedForUser('published', $user));
	}

	public function testUpdateContentAndApproved(IntegrationTester $I)
	{
		$newContent = 'Hello World';
		$q = $I->insertInDatabase(1, 'Quote', ['approved' => Quote::WAITING]);

		$this->repo->updateContentAndApproved($q->id, $newContent, Quote::PUBLISHED);

		$q = $this->repo->getById($q->id);
		$I->assertEquals(Quote::PUBLISHED, $q->approved);
		$I->assertEquals($newContent, $q->content);
	}

	public function testUpdateApproved(IntegrationTester $I)
	{
		$q = $I->insertInDatabase(1, 'Quote', ['approved' => Quote::WAITING]);

		$this->repo->updateApproved($q->id, Quote::PUBLISHED);

		$q = $this->repo->getById($q->id);
		$I->assertEquals(Quote::PUBLISHED, $q->approved);
	}

	public function testTotalPublished(IntegrationTester $I)
	{
		$I->insertInDatabase(3, 'Quote', ['approved' => Quote::WAITING]);
		$I->insertInDatabase(4, 'Quote', ['approved' => Quote::PUBLISHED]);

		$I->assertEquals(4, $this->repo->totalPublished());
	}

	public function testSubmittedTodayForUser(IntegrationTester $I)
	{
		$user = $I->insertInDatabase(1, 'User');
		$I->insertInDatabase(2, 'Quote', ['user_id' => $user->id, 'created_at' => Carbon::now()->subMonth(1)]);
		$I->insertInDatabase(3, 'Quote', ['user_id' => $user->id]);

		$I->assertEquals(3, $this->repo->submittedTodayForUser($user));
	}

	public function testCreateQuoteForUser(IntegrationTester $I)
	{
		$content = 'Hello World';
		$user = $I->insertInDatabase(1, 'User');

		$q = $this->repo->createQuoteForUser($user, $content);
		$q = $this->repo->getById($q->id);

		$I->assertEquals($content, $q->content);
		$I->assertEquals(Quote::WAITING, $q->approved);
	}

	public function testIndex(IntegrationTester $I)
	{
		$I->insertInDatabase(5, 'Quote', ['approved' => Quote::PUBLISHED]);
		$I->insertInDatabase(5, 'Quote', ['approved' => Quote::PUBLISHED, 'created_at' => Carbon::now()->subMonth(1)]);
		$I->insertInDatabase(3, 'Quote', ['approved' => Quote::WAITING]);

		$quotes = $this->repo->index(1, 5);
		$ids = $quotes->lists('id');
		sort($ids);
		$I->assertIsCollection($quotes);
		$I->assertEquals(range(1, 5), $ids);
	}

	public function testRandomPublished(IntegrationTester $I)
	{
		$I->insertInDatabase(10, 'Quote', ['approved' => Quote::PUBLISHED]);
		$I->insertInDatabase(20, 'Quote', ['approved' => Quote::REFUSED]);

		$quote = $this->repo->randomPublished(1);
		$I->assertTrue($quote instanceof Illuminate\Database\Eloquent\Collection);
		$I->assertEquals(1, count($quote));
		$I->assertEquals(Quote::PUBLISHED, $quote->first()->approved);
	}

	public function testRandomPublishedToday(IntegrationTester $I)
	{
		$lastMonth = Carbon::now()->subMonth(1);
		$I->insertInDatabase(5, 'Quote', ['approved' => Quote::PUBLISHED, 'created_at' => $lastMonth, 'updated_at' => $lastMonth]);
		$q = $I->insertInDatabase(1, 'Quote', ['approved' => Quote::PUBLISHED]);

		$quote = $this->repo->randomPublishedToday(1);
		$I->assertTrue($quote instanceof Illuminate\Database\Eloquent\Collection);
		$I->assertEquals(1, count($quote));
		$I->assertEquals(Quote::PUBLISHED, $quote->first()->approved);
		$I->assertEquals($q->id, $quote->first()->id);
	}

	public function testIndexRandom(IntegrationTester $I)
	{
		$I->insertInDatabase(20, 'Quote', ['approved' => Quote::REFUSED]);
		$I->insertInDatabase(5, 'Quote', ['approved' => Quote::PUBLISHED]);

		$quotes = $this->repo->indexRandom(1, 5);
		$I->assertIsCollection($quotes);
		$I->assertEquals(5, count($quotes));
		$ids = $quotes->lists('id');
		foreach ($quotes as $quote) {
			$I->assertGreaterThan(20, $quote->id);
		}

		// Check that for the second call we get the same quotes
		$quotes = $this->repo->indexRandom(1, 5);
		foreach ($quotes as $quote) {
			$I->assertTrue(in_array($quote->id, $ids));
		}
	}

	public function testListPublishedIdsForUser(IntegrationTester $I)
	{
		$I->insertInDatabase(5, 'Quote', ['approved' => Quote::PUBLISHED]);
		$user = $I->insertInDatabase(1, 'User');
		$I->insertInDatabase(2, 'Quote', ['approved' => Quote::PUBLISHED, 'user_id' => $user->id]);

		$ids = $this->repo->listPublishedIdsForUser($user);
		$I->assertEquals([6, 7], $ids);
	}

	public function testNbPublishedForUser(IntegrationTester $I)
	{
		$I->insertInDatabase(5, 'Quote', ['approved' => Quote::PUBLISHED]);
		$user = $I->insertInDatabase(1, 'User');
		$I->insertInDatabase(2, 'Quote', ['approved' => Quote::PUBLISHED, 'user_id' => $user->id]);

		$I->assertEquals(2, $this->repo->nbPublishedForUser($user));
	}

	public function testGetForIds(IntegrationTester $I)
	{
		$I->insertInDatabase(5, 'Quote', ['approved' => Quote::PUBLISHED]);
		$quotes = $this->repo->getForIds(range(1, 5), 1, 2);

		$I->assertEquals([1, 2], $quotes->lists('id'));
	}

	public function testGetQuotesByApprovedForUser(IntegrationTester $I)
	{
		$user = $I->insertInDatabase(1, 'User');
		$I->insertInDatabase(3, 'Quote', ['approved' => Quote::PUBLISHED, 'user_id' => $user->id]);

		$quotes = $this->repo->getQuotesByApprovedForUser($user, 'published', 1, 2);
		$I->assertEquals([1, 2], $quotes->lists('id'));
	}

	public function testNbDaysUntilPublication(IntegrationTester $I)
	{
		$nbQuotesPublishedPerDay = Config::get('app.quotes.nbQuotesToPublishPerDay');

		$I->insertInDatabase(1, 'Quote');
		$firstQuote = $I->insertInDatabase(1, 'Quote', ['approved' => Quote::PENDING]);

		// We can pass an ID
		$I->assertEquals(1, $this->repo->nbDaysUntilPublication($firstQuote->id));
		$I->insertInDatabase($nbQuotesPublishedPerDay, 'Quote', ['approved' => Quote::PENDING]);
		// We can pass an object
		$I->assertEquals(1, $this->repo->nbDaysUntilPublication($firstQuote));
		// It computes the right number for multiple days
		$I->assertEquals(1, $this->repo->nbDaysUntilPublication($nbQuotesPublishedPerDay + 1));
		$I->assertEquals(2, $this->repo->nbDaysUntilPublication($nbQuotesPublishedPerDay + 2));
	}

	public function testNbQuotesWithFavorites(IntegrationTester $I)
	{
		$I->insertInDatabase(2, 'Quote');
		$I->assertEquals(0, $this->repo->nbQuotesWithFavorites());

		$I->insertInDatabase(1, 'FavoriteQuote', ['quote_id' => 1]);
		$I->assertEquals(1, $this->repo->nbQuotesWithFavorites());

		$I->insertInDatabase(2, 'FavoriteQuote', ['quote_id' => 2]);
		$I->assertEquals(2, $this->repo->nbQuotesWithFavorites());
	}

	public function testGetQuotesForTag(IntegrationTester $I)
	{
		$quotes = $I->insertInDatabase(2, 'Quote');
		$tags = $I->insertInDatabase(2, 'Tag');

		$I->assertEmpty($this->repo->getQuotesForTag($tags[0], 1, 10));

		$this->tagRepo->tagQuote($quotes[0], $tags[0]);

		$quotesResult = $this->repo->getQuotesForTag($tags[0], 1, 10);
		$I->assertIsCollection($quotesResult);
		$I->assertEquals(1, count($quotesResult));

		// Add another quote with this tag
		$this->tagRepo->tagQuote($quotes[1], $tags[0]);
		$quotesResult = $this->repo->getQuotesForTag($tags[0], 1, 10);
		$I->assertEquals(2, count($quotesResult));
	}
}