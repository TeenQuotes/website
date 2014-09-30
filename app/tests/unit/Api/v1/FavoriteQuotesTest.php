<?php

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Laracasts\TestDummy\Factory;

class FavoriteQuotesTest extends ApiTest {

	protected $requiredAttributes = ['id', 'quote_id', 'user_id', 'created_at', 'updated_at'];
	protected $user;
	protected $idRefusedQuote;
	protected $idPublishedQuote;
	
	public function setUp()
	{
		parent::setUp();

		$this->controller = App::make('TeenQuotes\Api\V1\Controllers\FavQuotesController');
		
		// Create a user and log him in
		$user = Factory::create('User');
		$this->user = $this->logUserWithId($user['id']);
		
		$this->idRefusedQuote = $this->getIdRefusedQuote();
		$this->idPublishedQuote = $this->getIdPublishedQuote();
	}

	public function testPostQuoteNotFound()
	{
		$this->post($this->getIdNonExistingRessource());

		$this->assertStatusCodeIs(Response::HTTP_BAD_REQUEST)
			->withStatusMessage('quote_not_found')
			->withErrorMessage("The quote #".$this->getIdNonExistingRessource().' was not found.');
	}

	public function testPostQuoteNotPublished()
	{		
		$this->post($this->idRefusedQuote);

		$this->assertStatusCodeIs(Response::HTTP_BAD_REQUEST)
			->withStatusMessage('quote_not_published')
			->withErrorMessage("The quote #".$this->idRefusedQuote.' is not published.');
	}

	public function testPostQuoteSuccess()
	{
		$idPublishedQuote = $this->idPublishedQuote;
		$quote = Quote::find($idPublishedQuote);

		$this->post($idPublishedQuote);

		$this->assertStatusCodeIs(Response::HTTP_CREATED)
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

		$this->assertStatusCodeIs(Response::HTTP_BAD_REQUEST)
			->withStatusMessage('quote_already_favorited')
			->withErrorMessage("The quote #".$this->idPublishedQuote.' was already favorited.');
	}

	public function testDeleteQuoteNotFound()
	{
		$this->delete($this->getIdNonExistingRessource());

		$this->assertStatusCodeIs(Response::HTTP_BAD_REQUEST)
			->withStatusMessage('quote_not_found')
			->withErrorMessage("The quote #".$this->getIdNonExistingRessource().' was not found.');
	}

	public function testDeleteQuoteSuccess()
	{
		$quote = Quote::find($this->idPublishedQuote);

		// Add to favorite and run all assertions
		$this->testPostQuoteSuccess();
		
		// Delete it from favorites
		$this->delete($this->idPublishedQuote);

		$this->assertStatusCodeIs(Response::HTTP_OK)
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
		$this->response = $this->controller->postFavorite($quote_id);
		$this->bindJson();
	}

	private function delete($quote_id)
	{
		$this->response = $this->controller->deleteFavorite($quote_id);
		$this->bindJson();
	}

	private function getIdRefusedQuote()
	{
		$quote = Factory::create('Quote', ['approved' => Quote::REFUSED]);

		return $quote['id'];
	}

	private function getIdPublishedQuote()
	{
		$quote = Factory::create('Quote', ['approved' => Quote::PUBLISHED]);

		return $quote['id'];
	}
}