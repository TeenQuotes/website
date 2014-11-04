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
	}

	/**
	 * @expectedException        TeenQuotes\Exceptions\ApiNotFoundException
	 * @expectedExceptionMessage stories
	 */
	public function testIndexNotFound()
	{
		$this->tryPaginatedContentNotFound();
	}

	/**
	 * @expectedException Laracasts\Validation\FormValidationException
	 * @expectedExceptionMessage The frequence txt field is required.
	 */
	public function testStoreNoFrequence()
	{
		$this->logUserWithId(1);
		
		$this->addInputReplace([
			'frequence_txt' => '',
			'represent_txt' => $this->generateString(200)
		]);

		$this->tryStore();
	}

	/**
	 * @expectedException Laracasts\Validation\FormValidationException
	 * @expectedExceptionMessage The frequence txt must be at least 100 characters.
	 */
	public function testStoreTooSmallFrequence()
	{
		$this->logUserWithId(1);
		
		$this->addInputReplace([
			'frequence_txt' => $this->generateString(50),
			'represent_txt' => $this->generateString(200)
		]);

		$this->tryStore();
	}

	/**
	 * @expectedException Laracasts\Validation\FormValidationException
	 * @expectedExceptionMessage The frequence txt may not be greater than 1000 characters.
	 */
	public function testStoreTooLargeFrequence()
	{
		$this->logUserWithId(1);
		
		$this->addInputReplace([
			'frequence_txt' => $this->generateString(1001),
			'represent_txt' => $this->generateString(200)
		]);

		$this->tryStore();
	}

	/**
	 * @expectedException Laracasts\Validation\FormValidationException
	 * @expectedExceptionMessage The represent txt field is required.
	 */
	public function testStoreNoRepresent()
	{
		$this->logUserWithId(1);
		
		$this->addInputReplace([
			'represent_txt' => '',
			'frequence_txt' => $this->generateString(200)
		]);

		$this->tryStore();
	}

	/**
	 * @expectedException Laracasts\Validation\FormValidationException
	 * @expectedExceptionMessage The represent txt must be at least 100 characters.
	 */
	public function testStoreTooSmallRepresent()
	{
		$this->logUserWithId(1);
		
		$this->addInputReplace([
			'represent_txt' => $this->generateString(50),
			'frequence_txt' => $this->generateString(200)
		]);

		$this->tryStore();
	}

	/**
	 * @expectedException Laracasts\Validation\FormValidationException
	 * @expectedExceptionMessage The represent txt may not be greater than 1000 characters.
	 */
	public function testStoreTooLargeRepresent()
	{
		$this->logUserWithId(1);
		
		$this->addInputReplace([
			'represent_txt' => $this->generateString(1001),
			'frequence_txt' => $this->generateString(200)
		]);

		$this->tryStore();
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
}