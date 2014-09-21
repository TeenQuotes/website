<?php

use Laracasts\TestDummy\Factory;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Input;

class CountriesTest extends ApiTest {

	protected $contentType = 'countries';
	protected $containsSmallUser = false;
	protected $requiredAttributes = ['id', 'name'];

	public function setUp()
	{
		parent::setUp();
		
		Factory::times($this->nbRessources)->create('Country');

		$this->controller = App::make('TeenQuotes\Api\V1\Controllers\CountriesController');
	}

	public function testShowNotFound()
	{
		// Not found country
		$this->tryShowNotFound()
			->withStatusMessage('country_not_found')
			->withErrorMessage('The country #'.$this->getIdNonExistingRessource().' was not found');

	}

	public function testShowFound()
	{
		// Regular country
		for ($i = 1; $i <= $this->nbRessources; $i++)
			$this->tryShowFound($i);
	}

	public function testListCountries()
	{
		// List all countries
		$this->doRequest('show');
		$object = $this->getDecodedJson(); 

		$this->assertCount($this->nbRessources, $object);
		foreach ($object as $country)
			$this->assertObjectHasRequiredAttributes($country);
	}
}