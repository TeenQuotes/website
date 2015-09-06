<?php

/*
 * This file is part of the Teen Quotes website.
 *
 * (c) Antoine Augusti <antoine.augusti@teen-quotes.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TeenQuotes\Countries\Presenters;

use Laracasts\Presenter\Presenter;
use URL;

class CountryPresenter extends Presenter
{
    /**
     * The CSS class used to display the flag associated with
     * the country.
     *
     * @return string
     */
    public function countryCodeClass()
    {
        $countryCode = strtolower($this->entity->country_code);

        return 'flag-'.$countryCode;
    }

    public function searchUsers()
    {
        return URL::route('search.users.country', $this->entity->id);
    }
}
