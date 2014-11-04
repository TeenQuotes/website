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
		$this->hitStore(0, 200);
	}

	/**
	 * @expectedException Laracasts\Validation\FormValidationException
	 * @expectedExceptionMessage The frequence txt must be at least 100 characters.
	 */
	public function testStoreTooSmallFrequence()
	{
		$this->hitStore(50, 200);
	}

	/**
	 * @expectedException Laracasts\Validation\FormValidationException
	 * @expectedExceptionMessage The frequence txt may not be greater than 1000 characters.
	 */
	public function testStoreTooLargeFrequence()
	{
		$this->hitStore(1001, 200);
	}

	/**
	 * @expectedException Laracasts\Validation\FormValidationException
	 * @expectedExceptionMessage The represent txt field is required.
	 */
	public function testStoreNoRepresent()
	{
		$this->hitStore(200, 0);
	}

	/**
	 * @expectedException Laracasts\Validation\FormValidationException
	 * @expectedExceptionMessage The represent txt must be at least 100 characters.
	 */
	public function testStoreTooSmallRepresent()
	{
		$this->hitStore(200, 50);
	}

	/**
	 * @expectedException Laracasts\Validation\FormValidationException
	 * @expectedExceptionMessage The represent txt may not be greater than 1000 characters.
	 */
	public function testStoreTooLargeRepresent()
	{
		$this->hitStore(200, 1001);
	}

	public function testStoreSuccess()
	{
		$this->hitStore(200, 200);
		
		$this->assertStatusCodeIs(Response::HTTP_CREATED)
			->assertBelongsToLoggedInUser();

		// Check that we can retrieve the new item
		$this->tryShowFound($this->nbRessources + 1);
	}

	/**
	 * Hit the store endpoint
	 * @param  int $frequenceLength The length of the frequence_txt field
	 * @param  int $representLength The length of the reprensent_txt field
	 */
	private function hitStore($frequenceLength, $representLength)
	{
		$this->logUserWithId(1);

		$this->addInputReplace([
			'frequence_txt' => $this->generateString($frequenceLength),
			'represent_txt' => $this->generateString($representLength),
		]);

		$this->tryStore();
	}
}