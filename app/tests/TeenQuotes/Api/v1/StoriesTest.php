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
		$this->tryShowNotFound('story_not_found', 'The story #4 was not found');

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
		$this->tryPaginatedContentNotFound(404, 'No stories have been found.');
	}
}