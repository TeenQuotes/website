<?php namespace TeenQuotes\Countries\Localisation;

use Symfony\Component\HttpFoundation\Request;

class Detector implements CityDetector, CountryDetector {

	/**
	 * @var \TeenQuotes\Countries\Localisation\CityDetector
	 */
	private $cityDetector;

	/**
	 * @var \TeenQuotes\Countries\Localisation\CountryDetector
	 */
	private $countryDetector;

	public function __construct(CityDetector $cityDetector, CountryDetector $countryDetector)
	{
		$this->cityDetector    = $cityDetector;
		$this->countryDetector = $countryDetector;
	}

	public function detectCountry(Request $request)
	{
		return $this->countryDetector->detectCountry($request);
	}

	public function detectCity(Request $request)
	{
		return $this->cityDetector->detectCity($request);
	}
}