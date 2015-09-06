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

interface CountryRepository
{
    /**
     * Retrieve a country by its id.
     *
     * @param int $id
     *
     * @return \TeenQuotes\Countries\Models\Country
     */
    public function findById($id);

    /**
     * List all name and IDs for countries.
     *
     * @return array
     */
    public function listNameAndId();

    /**
     * Retrieve all countries.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll();
}
