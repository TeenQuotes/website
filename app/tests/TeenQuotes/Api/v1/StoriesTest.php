<?php

use Laracasts\TestDummy\Factory;
use Laracasts\TestDummy\DbTestCase;

class StoriesTest extends DbTestCase {

	protected $contentType = 'stories';
	protected $nbRessources = 3;
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
		$this->response = $this->controller->show(4);
		$this->assertResponseIsNotFound();
		$json = $this->retrieveJson();
		$this->assertEquals('story_not_found', $json->status);
		$this->assertEquals('The story #4 was not found', $json->error);

		// Regular story
		$this->response = $this->controller->show(1);
		$this->assertResponseHasSmallUser();
		$this->assertResponseHasAttributes($this->requiredAttributes);
	}

	public function testIndex()
	{
		// Test with the middle page
		Input::replace([
			'page'     => 2,
			'pagesize' => 1
		]);

		$this->response = $this->controller->index();
		$this->assertIsPaginatedResponse();
		$this->assertHasNextAndPreviousPage();
		
		$objectName = $this->contentType;
		$objects = $this->retrieveJson()->$objectName;
		$this->assertObjectContainsSmallUser(reset($objects));
		$this->assertObjectHasAttributes(reset($objects), $this->requiredAttributes);

		$this->assertNeighborsPagesMatch(2, 1);
	}
}