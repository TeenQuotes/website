<?php

/*
 * This file is part of the Teen Quotes website.
 *
 * (c) Antoine Augusti <antoine.augusti@teen-quotes.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TeenQuotes\Users\Composers;

use Lang;

class SearchUsersCountryComposer
{
    /**
     * Add data to a view.
     *
     * @param \Illuminate\View\View $view
     */

    /**
     * Add data to the view.
     *
     * @param \Illuminate\View\View $view
     */
    public function compose($view)
    {
        $data        = $view->getData();
        $country     = $data['country'];
        $countryName = $country->name;

        $view->with('pageTitle', Lang::get('search.usersCountryPageTitle', compact('countryName')));
        $view->with('pageDescription', Lang::get('search.usersCountryPageDescription', compact('countryName')));
    }
}
