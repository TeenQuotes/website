<?php


class CountriesTest extends ApiTest
{
    protected $requiredAttributes = ['id', 'name', 'country_code'];

    protected function _before()
    {
        parent::_before();

        $this->unitTester->insertInDatabase($this->unitTester->getNbRessources(), 'Country');

        $this->unitTester->setContentType('comments');

        $this->unitTester->setController(App::make('TeenQuotes\Api\V1\Controllers\CountriesController'));
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
        for ($i = 1; $i <= $this->unitTester->getNbRessources(); $i++) {
            $this->unitTester->tryShowFound($i);
        }
    }

    public function testListCountries()
    {
        // List all countries
        $this->unitTester->doRequest('show');
        $object = $this->unitTester->getDecodedJson();

        $this->assertCount($this->unitTester->getNbRessources(), $object);
        foreach ($object as $country) {
            $this->unitTester->assertObjectHasRequiredAttributes($country);
        }
    }
}
