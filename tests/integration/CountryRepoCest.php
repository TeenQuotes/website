<?php

/*
 * This file is part of the Teen Quotes website.
 *
 * (c) Antoine Augusti <antoine.augusti@teen-quotes.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use TeenQuotes\Countries\Models\Country;

class CountryRepoCest
{
    /**
     * @var \TeenQuotes\Countries\Repositories\CountryRepository
     */
    private $repo;

    public function _before()
    {
        $this->repo = App::make('TeenQuotes\Countries\Repositories\CountryRepository');

        Country::truncate();
    }

    public function testFindById(IntegrationTester $I)
    {
        $c = $I->insertInDatabase(1, 'Country');

        $country = $this->repo->findById($c->id);

        $I->assertEquals($c->name, $country->name);
        $I->assertEquals($c->country_code, $country->country_code);
    }

    public function testListNameAndId(IntegrationTester $I)
    {
        $I->insertInDatabase(1, 'Country', ['name' => 'ab']);
        $I->insertInDatabase(1, 'Country', ['name' => 'cd']);

        $expected = [
            1 => 'ab',
            2 => 'cd',
        ];

        $I->assertEquals($expected, $this->repo->listNameAndId());
    }

    public function testGetAll(IntegrationTester $I)
    {
        $I->insertInDatabase(1, 'Country', ['name' => 'ab']);
        $I->insertInDatabase(1, 'Country', ['name' => 'cd']);

        $countries = $this->repo->getAll();

        $I->assertIsCollection($countries);

        $ids = $countries->lists('id');
        sort($ids);
        $I->assertEquals([1, 2], $ids);

        $names = $countries->lists('name');
        sort($names);
        $I->assertEquals(['ab', 'cd'], $names);
    }
}
