<?php

/*
 * This file is part of the Teen Quotes website.
 *
 * (c) Antoine Augusti <antoine.augusti@teen-quotes.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TeenQuotes\Countries\Repositories;

use TeenQuotes\Countries\Models\Country;

class DbCountryRepository implements CountryRepository
{
    /**
     * Retrieve a country by its id.
     *
     * @param int $id
     *
     * @return \TeenQuotes\Countries\Models\Country
     */
    public function findById($id)
    {
        return Country::find($id);
    }

    /**
     * List all name and IDs for countries.
     *
     * @return array
     */
    public function listNameAndId()
    {
        return Country::lists('name', 'id');
    }

    /**
     * Retrieve all countries.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll()
    {
        return Country::all();
    }
}
