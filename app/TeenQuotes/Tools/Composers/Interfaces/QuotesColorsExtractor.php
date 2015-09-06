<?php

/*
 * This file is part of the Teen Quotes website.
 *
 * (c) Antoine Augusti <antoine.augusti@teen-quotes.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TeenQuotes\Tools\Composers\Interfaces;

interface QuotesColorsExtractor
{
    /**
     * Extract associative array of #quote->id => "color" for a collection of quotes
     * Should also save this array in session.
     *
     * @param \Illuminate\Database\Eloquent\Collection $quotes The quotes collection
     *
     * @return array The associative array
     */
    public function extractAndStoreColors($quotes);
}
