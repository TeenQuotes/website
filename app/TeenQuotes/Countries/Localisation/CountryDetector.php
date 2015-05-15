<?php

namespace TeenQuotes\Countries\Localisation;

use Symfony\Component\HttpFoundation\Request;

interface CountryDetector
{
    /**
     * Detect the country for a HTTP request.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @see    \TeenQuotes\Countries\Models\Country
     *
     * @return int|null The ID of the detected country
     *                  If we can't find a match, return null
     */
    public function detectCountry(Request $request);
}
