<?php

use Laracasts\TestDummy\Factory;

class StoriesTest extends ApiTest {

	protected $contentType = 'stories';
	protected $containsSmallUser = true;
	protected $requiredAttributes = ['id', 'represent_txt', 'frequence_txt', 'user_id', 'created_at', 'updated_at'];

	public function setUp()
	{
		parent::setUp();
		
		Factory::times($this->nbRessources)->create('Story');

		$this->controller = App::make('TeenQuotes\Api\V1\Controllers\StoriesController');
	}

	public function testShow()
	{
		// No found story
		$this->tryShowNotFound()
			->withStatusMessage('story_not_found')
			->withErrorMessage('The story #4 was not found');

		// Regular story
		$this->tryShowFound(1);	
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

	public function testStore()
	{
		$this->logUserWithId(1);

		// Too small values
		foreach (['frequence_txt', 'represent_txt'] as $value) {
			
			$otherValue = $this->otherValue($value);
			Input::replace([
				$value => $this->faker->text(50),
				$otherValue => $this->faker->text(200)
			]);
			
			$this->tryStore()
				->assertStatusCodeIs(400)
				->withStatusMessage('wrong_'.$value);
		}

		// Too large values
		foreach (['frequence_txt', 'represent_txt'] as $value) {
			
			$otherValue = $this->otherValue($value);
			Input::replace([
				$value => $this->faker->text(1500),
				$otherValue => $this->faker->text(200)
			]);
			
			$this->tryStore()
				->assertStatusCodeIs(400)
				->withStatusMessage('wrong_'.$value);
		}

		// Successfull store
		Input::replace([
			'represent_txt' => $this->faker->text(200),
			'frequence_txt' => $this->faker->text(200)
		]);
		
		$this->tryStore()
			->assertStatusCodeIs(201)
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