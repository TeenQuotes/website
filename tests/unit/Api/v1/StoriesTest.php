<?php

use Laracasts\TestDummy\Factory;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Input;

class StoriesTest extends ApiTest {

	protected $contentType = 'stories';
	protected $embedsRelation = ['user_small'];
	protected $requiredAttributes = ['id', 'represent_txt', 'frequence_txt', 'user_id', 'created_at', 'updated_at'];

	public function setUp()
	{
		parent::setUp();
		
		Factory::times($this->nbRessources)->create('TeenQuotes\Stories\Models\Story');

		$this->controller = App::make('TeenQuotes\Api\V1\Controllers\StoriesController');
	}

	public function testShowNotFound()
	{
		// Not found story
		$this->tryShowNotFound()
			->withStatusMessage('story_not_found')
			->withErrorMessage('The story #'.$this->getIdNonExistingRessource().' was not found.');

	}

	public function testShowFound()
	{
		// Regular story
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
			->withErrorMessage('No stories have been found.');
	}

	public function testStoreErrors()
	{
		$this->logUserWithId(1);

		// Too small values
		foreach (['frequence_txt', 'represent_txt'] as $value) {
			
			$otherValue = $this->otherValue($value);
			$this->addInputReplace([
				$value      => $this->generateString(50),
				$otherValue => $this->generateString(200)
			]);
			
			$this->tryStore()
				->assertStatusCodeIs(Response::HTTP_BAD_REQUEST)
				->withStatusMessage('wrong_'.$value);
		}

		// Too large values
		foreach (['frequence_txt', 'represent_txt'] as $value) {
			
			$otherValue = $this->otherValue($value);
			$this->addInputReplace([
				$value      => $this->generateString(1500),
				$otherValue => $this->generateString(200)
			]);
			
			$this->tryStore()
				->assertStatusCodeIs(Response::HTTP_BAD_REQUEST)
				->withStatusMessage('wrong_'.$value);
		}
	}

	public function testStoreSuccess()
	{
		$this->logUserWithId(1);
		
		// Successfull store
		$this->addInputReplace([
			'represent_txt' => $this->generateString(200),
			'frequence_txt' => $this->generateString(200)
		]);
		
		$this->tryStore()
			->assertStatusCodeIs(Response::HTTP_CREATED)
			->assertBelongsToLoggedInUser();

		// Check that we can retrieve the new item
		$this->tryShowFound($this->nbRessources + 1);
	}

	private function otherValue($value)
	{
		if ( ! in_array($value, ['frequence_txt', 'represent_txt']))
			throw new \InvalidArgumentException("Expecting frequence_txt|represent_txt", 1);
			
		if ($value == 'frequence_txt')
			return 'represent_txt';

		return 'frequence_txt';
	}
}