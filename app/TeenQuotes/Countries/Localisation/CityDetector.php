<?php

/*
 * This file is part of the Teen Quotes website.
 *
 * (c) Antoine Augusti <antoine.augusti@teen-quotes.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TeenQuotes\Countries\Localisation;

use Symfony\Component\HttpFoundation\Request;

interface CityDetector
{
    /**
     * Detect the city for a HTTP request.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @see    \TeenQuotes\Countries\Models\Country
     *
     * @return string|null The name of the detected city
     *                     If we can't find a match, return null
     */
    public function detectCity(Request $request);
}
