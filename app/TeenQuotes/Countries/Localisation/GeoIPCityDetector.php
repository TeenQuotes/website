<?php

namespace TeenQuotes\Countries\Localisation;

use Buonzz\GeoIP\GeoIP;
use Exception;
use Symfony\Component\HttpFoundation\Request;

class GeoIPCityDetector implements CityDetector
{
    /**
     * @var \Buonzz\GeoIP\GeoIP
     */
    private $detector;

    public function __construct(GeoIP $detector)
    {
        $this->detector  = $detector;
    }

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
    public function detectCity(Request $request)
    {
        $this->detector->setIP($request->getClientIp());

        try {
            $cityDetected = $this->detector->getCity();

            return $cityDetected;
        } catch (Exception $e) {
            return null;
        }
    }
}
