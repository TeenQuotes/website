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

use App;
use Buonzz\GeoIP\GeoIP;
use Exception;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Request;

class GeoIPCountryDetector implements CountryDetector
{
    /**
     * The IDs of available countries.
     *
     * @var array
     */
    private $ids;

    /**
     * The name of available of countries.
     *
     * @var array
     */
    private $countries;

    /**
     * The default country if we can't find a match.
     *
     * @var null|int
     */
    private $defaultID = null;

    /**
     * @var \Buonzz\GeoIP\GeoIP
     */
    private $detector;

    public function __construct($ids, $countries, GeoIP $detector)
    {
        $this->ids       = $ids;
        $this->countries = $countries;
        $this->detector  = $detector;
    }

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
    public function detectCountry(Request $request)
    {
        $this->detector->setIP($request->getClientIp());

        try {
            $detectedCountry = $this->detector->getCountry();
        } catch (Exception $e) {
            $detectedID = $this->defaultID;
        }

        // Create a key value pair for each country
        $countries = array_combine($this->ids, $this->countries);

        // If the detected country in the possible countries, we will select it
        if (!isset($detectedID) and in_array($detectedCountry, $this->countries)) {
            $detectedID = array_search($detectedCountry, $this->countries);
        } else {
            $detectedID = $this->defaultID;
        }

        return $detectedID;
    }

    /**
     * Set the default ID to return if we don't match something.
     *
     * @param int $id
     */
    public function setDefault($id)
    {
        if (!is_int($id)) {
            throw new InvalidArgumentException($id.' is not an integer.');
        }

        if ($this->isProduction() and !in_array($id, $this->ids)) {
            throw new InvalidArgumentException($id.' was not in the list of all IDs.');
        }

        $this->defaultID = $id;
    }

    private function isProduction()
    {
        return App::environment() === 'production';
    }
}
