<?php

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use TeenQuotes\Quotes\Models\FavoriteQuote;
use TeenQuotes\Quotes\Models\Quote;

class QuotesTest extends ApiTest {

	protected $requiredAttributes = ['id', 'content', 'user_id', 'approved', 'created_at', 'has_comments', 'total_comments', 'is_favorite'];
	protected $embedsRelation = ['user_small'];
	protected $approvedTypes = ['waiting', 'refused', 'pending', 'published'];

	protected function _before()
	{
		parent::_before();
		
		$this->unitTester->setController(App::make('TeenQuotes\Api\V1\Controllers\QuotesController'));

		$this->unitTester->setContentType('quotes');

		$this->unitTester->insertInDatabase($this->unitTester->getNbRessources(), 'Quote');
	}

	public function testShowNotFound()
	{
		// Not found quote
		$this->unitTester->tryShowNotFound()
			->withStatusMessage('quote_not_found')
			->withErrorMessage('The quote #'.$this->unitTester->getIdNonExistingRessource().' was not found.');
	}

	public function testShowFound()
	{
		// Regular quote
		for ($i = 1; $i <= $this->unitTester->getNbRessources(); $i++)
			$this->unitTester->tryShowFound($i);
	}

	public function testIndex()
	{
		// Test with the middle page
		$this->unitTester->tryMiddlePage();

		// Test first page
		$this->unitTester->tryFirstPage();
	}

	/**
	 * @expectedException        TeenQuotes\Exceptions\ApiNotFoundException
	 * @expectedExceptionMessage quotes
	 */
	public function testIndexNotFound()
	 {
		$this->unitTester->tryPaginatedContentNotFound();
	 }

	public function testIndexRandom()
	{
		// Test with the middle page
		$this->unitTester->tryMiddlePage('random');

		// Test first page
		$this->unitTester->tryFirstPage('random');
	}

	/**
	 * @expectedException        TeenQuotes\Exceptions\ApiNotFoundException
	 * @expectedExceptionMessage quotes
	 */
	public function testIndexRandomNotFound()
	 {
		$this->unitTester->tryPaginatedContentNotFound('random');
	 }

	public function testStoreContentTooSmall()
	{
		$this->unitTester->logUserWithId(1);

		// Content is too small
		$this->unitTester->addInputReplace(['content' => $this->unitTester->generateString(49)]);

		$this->assertStoreIsWrongContent();
	}

	public function testStoreContentTooLong()
	{
		$this->unitTester->logUserWithId(1);

		// Content is too long
		$this->unitTester->addInputReplace(['content' => $this->unitTester->generateString(301)]);

		$this->assertStoreIsWrongContent();
	}

	public function testStoreSuccess()
	{
		$this->unitTester->logUserWithId(1);

		$this->unitTester->addInputReplace(['content' => $this->unitTester->generateString(100)]);
		
		$this->unitTester->tryStore()
			->assertStatusCodeIs(Response::HTTP_CREATED)
			->assertBelongsToLoggedInUser();
	}

	public function testStoreTooMuchSubmittedQuotes()
	{
		// Simulate that a user has already posted 5 quotes today
		$u = $this->unitTester->insertInDatabase(1, 'User');
		$this->unitTester->insertInDatabase(5, 'Quote', ['user_id' => $u['id']]);

		$this->unitTester->addInputReplace(['content' => $this->unitTester->generateString(100)]);
		
		$this->unitTester->logUserWithId($u['id']);

		// Try to submit another quote but we should reach the limit
		$this->unitTester->tryStore()
			->assertStatusCodeIs(Response::HTTP_BAD_REQUEST)
			->withStatusMessage('too_much_submitted_quotes')
			->withErrorMessage('The maximum number of quotes you can submit is 5 per day.');
	}

	public function testQuotesFavoritesUnexistingUser()
	{
		$idNonExistingUser = 500;

		$this->unitTester->doRequest('indexFavoritesQuotes', $idNonExistingUser)
			->assertStatusCodeIs(Response::HTTP_BAD_REQUEST)
			->withStatusMessage('user_not_found')
			->withErrorMessage('The user #'.$idNonExistingUser.' was not found.');
	}

	/**
	 * @expectedException        TeenQuotes\Exceptions\ApiNotFoundException
	 * @expectedExceptionMessage quotes
	 */
	public function testQuotesFavoritesWithoutFavorites()
	{
		$u = $this->unitTester->insertInDatabase(1, 'User');

		$this->unitTester->doRequest('indexFavoritesQuotes', $u['id']);
	}

	public function testQuotesFavoritesSuccess()
	{
		// Create favorites for a user
		$u = $this->unitTester->insertInDatabase(1, 'User');
		for ($i = 1; $i <= $this->unitTester->getNbRessources(); $i++) { 
			$f = new FavoriteQuote;
			$f->user_id = $u['id'];
			$f->quote_id = $i;
			$f->save();
		}

		$this->unitTester->tryMiddlePage('indexFavoritesQuotes', $u['id']);

		$this->unitTester->tryFirstPage('indexFavoritesQuotes', $u['id']);
	}

	public function testQuotesByApprovedUnexistingUser()
	{
		$idNonExistingUser = 500;

		foreach ($this->approvedTypes as $approved) {
			
			$this->unitTester->doRequest('indexByApprovedQuotes', [$approved, $idNonExistingUser])
				->assertStatusCodeIs(Response::HTTP_BAD_REQUEST)
				->withStatusMessage('user_not_found')
				->withErrorMessage('The user #'.$idNonExistingUser.' was not found.');
		}
	}

	/**
	 * @expectedException        TeenQuotes\Exceptions\ApiNotFoundException
	 * @expectedExceptionMessage quotes
	 */
	public function testQuotesByApprovedUnexistingQuotes()
	{
		foreach ($this->approvedTypes as $approved) {
			
			// Create a new user each time, with no quotes
			$u = $this->unitTester->insertInDatabase(1, 'User');
			$idUser = $u['id'];
			$this->unitTester->logUserWithId($idUser);

			// Create a quote with a different approved type for this user
			$this->createQuotesForUserWithDifferentApproved($idUser, $approved);

			$this->unitTester->doRequest('indexByApprovedQuotes', [$approved, $idUser]);
		}
	}

	public function testQuotesByApprovedExistingQuotes()
	{
		$u = $this->unitTester->insertInDatabase(1, 'User');
		$idUser = $u['id'];
		$this->unitTester->logUserWithId($idUser);
		
		foreach ($this->approvedTypes as $approved) {
			
			// Create some quotes with the given approved type for this user
			$this->createQuotesForUserWithApproved($idUser, $approved);

			$this->unitTester->tryMiddlePage('indexByApprovedQuotes', [$approved, $idUser]);

			$this->unitTester->tryFirstPage('indexByApprovedQuotes', [$approved, $idUser]);
		}
	}

	private function createQuotesForUserWithApproved($id, $approved)
	{
		$approvedType = constant("TeenQuotes\Quotes\Models\Quote::".strtoupper($approved));
		
		$this->unitTester->insertInDatabase($this->unitTester->getNbRessources(), 'Quote', ['user_id' => $id, 'approved' => $approvedType]);
	}

	private function createQuotesForUserWithDifferentApproved($id, $approved)
	{
		if ($approved == 'published')
			$this->unitTester->insertInDatabase(1, 'Quote', ['user_id' => $id, 'approved' => Quote::WAITING]);
		
		$this->unitTester->insertInDatabase(1, 'Quote', ['user_id' => $id, 'approved' => Quote::PUBLISHED]);
	}

	private function assertStoreIsWrongContent()
	{
		$this->unitTester->tryStore()
			->assertStatusCodeIs(Response::HTTP_BAD_REQUEST)
			->withStatusMessage('wrong_content')
			->withErrorMessage('Content of the quote should be between 50 and 300 characters.');
	}

	private function disableEmbedsSmallUser()
	{
		$this->unitTester->setEmbedsRelation([]);
	}
}