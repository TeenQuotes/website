<?php

use Illuminate\Http\Response;

class CountriesTest extends ApiTest {

	protected $requiredAttributes = ['id', 'name'];

	protected function _before()
	{
		parent::_before();
		
		$this->unitTester->insertInDatabase($this->apiHelper->nbRessources, 'Country');

		$this->apiHelper->setContentType('comments');

		$this->apiHelper->controller = App::make('TeenQuotes\Api\V1\Controllers\CountriesController');
	}

	public function testShowNotFound()
	{
		// Not found country
		$this->unitTester->tryShowNotFound()
			->withStatusMessage('country_not_found')
			->withErrorMessage('The country #'.$this->unitTester->getIdNonExistingRessource().' was not found.');
	}

	public function testShowFound()
	{
		// Regular country
		for ($i = 1; $i <= $this->apiHelper->nbRessources; $i++)
			$this->unitTester->tryShowFound($i);
	}

	public function testListCountries()
	{
		// List all countries
		$this->unitTester->doRequest('show');
		$object = $this->unitTester->getDecodedJson(); 

		$this->assertCount($this->apiHelper->nbRessources, $object);
		foreach ($object as $country)
			$this->apiHelper->assertObjectHasRequiredAttributes($country);
	}
}