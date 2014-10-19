<?php

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Laracasts\TestDummy\Factory;
use TeenQuotes\Quotes\Models\FavoriteQuote;
use TeenQuotes\Quotes\Models\Quote;

class QuotesTest extends ApiTest {

	protected $requiredAttributes = ['id', 'content', 'user_id', 'approved', 'created_at', 'has_comments', 'total_comments', 'is_favorite'];
	protected $embedsRelation = ['user_small'];
	protected $contentType = 'quotes';
	protected $approvedTypes = ['waiting', 'refused', 'pending', 'published'];

	public function setUp()
	{
		parent::setUp();

		$this->controller = App::make('TeenQuotes\Api\V1\Controllers\QuotesController');

		Factory::times($this->nbRessources)->create('TeenQuotes\Quotes\Models\Quote');
	}

	public function testShowNotFound()
	{
		// Not found quote
		$this->tryShowNotFound()
			->withStatusMessage('quote_not_found')
			->withErrorMessage('The quote #'.$this->getIdNonExistingRessource().' was not found.');
	}

	public function testShowFound()
	{
		// Regular quote
		for ($i = 1; $i <= $this->nbRessources; $i++)
			$this->tryShowFound($i);
	}

	public function testIndex()
	{
		// Test with the middle page
		$this->tryMiddlePage();

		// Test first page
		$this->tryFirstPage();

		// Test not found
		$this->tryPaginatedContentNotFound()
			->withStatusMessage(404)
			->withErrorMessage('No quotes have been found.');
	}

	public function testIndexRandom()
	{
		// Test with the middle page
		$this->tryMiddlePage('index', 'random');

		// Test first page
		$this->tryFirstPage('index', 'random');

		// Test not found
		$this->tryPaginatedContentNotFound('random')
			->withStatusMessage(404)
			->withErrorMessage('No quotes have been found.');
	}

	public function testStoreContentTooSmall()
	{
		$this->logUserWithId(1);

		// Content is too small
		$this->addInputReplace(['content' => $this->generateString(49)]);

		$this->assertStoreIsWrongContent();
	}

	public function testStoreContentTooLong()
	{
		$this->logUserWithId(1);

		// Content is too long
		$this->addInputReplace(['content' => $this->generateString(301)]);

		$this->assertStoreIsWrongContent();
	}

	public function testStoreSuccess()
	{
		$this->logUserWithId(1);

		$this->addInputReplace(['content' => $this->generateString(100)]);
		
		$this->tryStore()
			->assertStatusCodeIs(Response::HTTP_CREATED)
			->assertBelongsToLoggedInUser();
	}

	public function testStoreTooMuchSubmittedQuotes()
	{
		// Simulate that a user has already posted 5 quotes today
		$u = Factory::create('TeenQuotes\Users\Models\User');
		Factory::times(5)->create('TeenQuotes\Quotes\Models\Quote', ['user_id' => $u['id']]);

		$this->addInputReplace(['content' => $this->generateString(100)]);
		
		$this->logUserWithId($u['id']);

		// Try to submit another quote but we should reach the limit
		$this->tryStore()
			->assertStatusCodeIs(Response::HTTP_BAD_REQUEST)
			->withStatusMessage('too_much_submitted_quotes')
			->withErrorMessage('The maximum number of quotes you can submit is 5 per day.');
	}

	public function testQuotesFavoritesUnexistingUser()
	{
		$idNonExistingUser = 500;

		$this->doRequest('indexFavoritesQuotes', $idNonExistingUser)
			->assertStatusCodeIs(Response::HTTP_BAD_REQUEST)
			->withStatusMessage('user_not_found')
			->withErrorMessage('The user #'.$idNonExistingUser.' was not found.');
	}

	public function testQuotesFavoritesWithoutFavorites()
	{
		$u = Factory::create('TeenQuotes\Users\Models\User');

		$this->doRequest('indexFavoritesQuotes', $u['id'])
			->assertStatusCodeIs(Response::HTTP_NOT_FOUND)
			->withStatusMessage(404)
			->withErrorMessage('No quotes have been found.');
	}

	public function testQuotesFavoritesSuccess()
	{
		// Create favorites for a user
		$u = Factory::create('TeenQuotes\Users\Models\User');
		for ($i = 1; $i <= $this->nbRessources; $i++) { 
			$f = new FavoriteQuote;
			$f->user_id = $u['id'];
			$f->quote_id = $i;
			$f->save();
		}

		$this->tryMiddlePage('indexFavoritesQuotes', $u['id']);

		$this->tryFirstPage('indexFavoritesQuotes', $u['id']);
	}

	public function testQuotesByApprovedUnexistingUser()
	{
		$idNonExistingUser = 500;

		foreach ($this->approvedTypes as $approved) {
			
			$this->doRequest('indexByApprovedQuotes', [$approved, $idNonExistingUser])
				->assertStatusCodeIs(Response::HTTP_BAD_REQUEST)
				->withStatusMessage('user_not_found')
				->withErrorMessage('The user #'.$idNonExistingUser.' was not found.');
		}
	}

	public function testQuotesByApprovedUnexistingQuotes()
	{
		foreach ($this->approvedTypes as $approved) {
			
			// Create a new user each time, with no quotes
			$u = Factory::create('TeenQuotes\Users\Models\User');
			$idUser = $u['id'];
			$this->logUserWithId($idUser);

			// Create a quote with a different approved type for this user
			$this->createQuotesForUserWithDifferentApproved($idUser, $approved);

			$this->doRequest('indexByApprovedQuotes', [$approved, $idUser])
				->assertResponseIsNotFound()
				->withStatusMessage(404)
				->withErrorMessage('No quotes have been found.');
		}
	}

	public function testQuotesByApprovedExistingQuotes()
	{
		$u = Factory::create('TeenQuotes\Users\Models\User');
		$idUser = $u['id'];
		$this->logUserWithId($idUser);
		
		foreach ($this->approvedTypes as $approved) {
			
			// Create some quotes with the given approved type for this user
			$this->createQuotesForUserWithApproved($idUser, $approved);

			$this->tryMiddlePage('indexByApprovedQuotes', [$approved, $idUser]);

			$this->tryFirstPage('indexByApprovedQuotes', [$approved, $idUser]);
		}
	}

	private function createQuotesForUserWithApproved($id, $approved)
	{
		$approvedType = constant("TeenQuotes\Quotes\Models\Quote::".strtoupper($approved));
		
		Factory::times($this->nbRessources)->create('TeenQuotes\Quotes\Models\Quote', ['user_id' => $id, 'approved' => $approvedType]);
	}

	private function createQuotesForUserWithDifferentApproved($id, $approved)
	{
		if ($approved == 'published')
			Factory::create('TeenQuotes\Quotes\Models\Quote', ['user_id' => $id, 'approved' => Quote::WAITING]);
		else
			Factory::create('TeenQuotes\Quotes\Models\Quote', ['user_id' => $id, 'approved' => Quote::PUBLISHED]);
	}

	private function assertStoreIsWrongContent()
	{
		$this->tryStore()
			->assertStatusCodeIs(Response::HTTP_BAD_REQUEST)
			->withStatusMessage('wrong_content')
			->withErrorMessage('Content of the quote should be between 50 and 300 characters.');
	}

	private function disableEmbedsSmallUser()
	{
		$this->embedsRelation = [];
	}
}