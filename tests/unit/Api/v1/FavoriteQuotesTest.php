<?php

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use TeenQuotes\Quotes\Models\FavoriteQuote;
use TeenQuotes\Quotes\Models\Quote;

class FavoriteQuotesTest extends ApiTest {

	protected $requiredAttributes = ['id', 'quote_id', 'user_id', 'created_at', 'updated_at'];
	protected $user;
	protected $idRefusedQuote;
	protected $idPublishedQuote;
	
	protected function _before()
	{
		parent::_before();
		
		$this->unitTester->setController(App::make('TeenQuotes\Api\V1\Controllers\QuotesFavoriteController'));
		
		// Create a user and log him in
		$user = $this->unitTester->insertInDatabase(1, 'User');
		$this->user = $this->unitTester->logUserWithId($user['id']);
		
		$this->idRefusedQuote = $this->getIdRefusedQuote();
		$this->idPublishedQuote = $this->getIdPublishedQuote();
	}

	public function testPostQuoteNotFound()
	{
		$this->post($this->unitTester->getIdNonExistingRessource());

		$this->unitTester->assertStatusCodeIs(Response::HTTP_BAD_REQUEST)
			->withStatusMessage('quote_not_found')
			->withErrorMessage("The quote #".$this->unitTester->getIdNonExistingRessource().' was not found.');
	}

	public function testPostQuoteNotPublished()
	{		
		$this->post($this->idRefusedQuote);

		$this->unitTester->assertStatusCodeIs(Response::HTTP_BAD_REQUEST)
			->withStatusMessage('quote_not_published')
			->withErrorMessage("The quote #".$this->idRefusedQuote.' is not published.');
	}

	public function testPostQuoteSuccess()
	{
		$idPublishedQuote = $this->idPublishedQuote;
		$quote = Quote::find($idPublishedQuote);

		$this->post($idPublishedQuote);

		$this->unitTester->assertStatusCodeIs(Response::HTTP_CREATED)
			->assertResponseHasRequiredAttributes();
		
		// Verify that the quote cache has been set
		$this->assertTrue($quote->total_favorites == 1);
		$this->assertTrue(Cache::get(Quote::$cacheNameNbFavorites.$idPublishedQuote) == 1);

		// Verify that the user cache has been set properly
		$this->assertEquals([$idPublishedQuote], $this->user->arrayIDFavoritesQuotes());
		$this->assertEquals([$idPublishedQuote], Cache::get(FavoriteQuote::$cacheNameFavoritesForUser.$this->user->id));
	}

	public function testPostQuoteAlreadyFavorited()
	{	
		// Add to favorite
		$this->post($this->idPublishedQuote);

		// Add to favorite again
		$this->post($this->idPublishedQuote);

		$this->unitTester->assertStatusCodeIs(Response::HTTP_BAD_REQUEST)
			->withStatusMessage('quote_already_favorited')
			->withErrorMessage("The quote #".$this->idPublishedQuote.' was already favorited.');
	}

	public function testDeleteQuoteNotFound()
	{
		$this->delete($this->unitTester->getIdNonExistingRessource());

		$this->unitTester->assertStatusCodeIs(Response::HTTP_BAD_REQUEST)
			->withStatusMessage('quote_not_found')
			->withErrorMessage("The quote #".$this->unitTester->getIdNonExistingRessource().' was not found.');
	}

	public function testDeleteQuoteSuccess()
	{
		$quote = Quote::find($this->idPublishedQuote);

		// Add to favorite and run all assertions
		$this->testPostQuoteSuccess();
		
		// Delete it from favorites
		$this->delete($this->idPublishedQuote);

		$this->unitTester->assertStatusCodeIs(Response::HTTP_OK)
			->withStatusMessage('favorite_deleted')
			->withSuccessMessage("The quote #".$this->idPublishedQuote.' was deleted from favorites.');

		// Verify that the quote cache has been deleted
		$this->assertTrue($quote->total_favorites == 0);
		$this->assertTrue(Cache::get(Quote::$cacheNameNbFavorites.$this->idPublishedQuote) == 0);

		// Verify that the user cache has been deleted properly
		$this->assertEmpty($this->user->arrayIDFavoritesQuotes());
		$this->assertEmpty(Cache::get(FavoriteQuote::$cacheNameFavoritesForUser.$this->user->id));
	}

	private function post($quote_id)
	{
		$this->unitTester->setResponse(
			$this->unitTester->getController()->postFavorite($quote_id)
		);
		
		$this->unitTester->bindJson(
			$this->unitTester->getResponse()->getContent()
		);
	}

	private function delete($quote_id)
	{
		$this->unitTester->setResponse(
			$this->unitTester->getController()->deleteFavorite($quote_id)
		);
		
		$this->unitTester->bindJson(
			$this->unitTester->getResponse()->getContent()
		);
	}

	private function getIdRefusedQuote()
	{
		$quote = $this->unitTester->insertInDatabase(1, 'Quote', ['approved' => Quote::REFUSED]);

		return $quote['id'];
	}

	private function getIdPublishedQuote()
	{
		$quote = $this->unitTester->insertInDatabase(1, 'Quote', ['approved' => Quote::PUBLISHED]);

		return $quote['id'];
	}
}