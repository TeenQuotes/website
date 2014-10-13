<?php namespace TeenQuotes\Api\V1\Controllers;

use TeenQuotes\Countries\Repositories\CountryRepository;
use TeenQuotes\Http\Facades\Response;

class CountriesController extends APIGlobalController {
	
	/**
	 * @var TeenQuotes\Countries\Repositories\CountryRepository
	 */
	private $countryRepo;
	
	function __construct(CountryRepository $countryRepo)
	{
		$this->countryRepo = $countryRepo;
	}

	public function show($country_id = null)
	{
		// List all countries
		if (is_null($country_id))
			return Response::json($this->countryRepo->getAll(), 200, [], JSON_NUMERIC_CHECK);
		
		// Get a single country
		$country = $this->countryRepo->findById($country_id);

		// Country not found
		if (is_null($country))
			return Response::json([
				'status' => 'country_not_found',
				'error'  => "The country #".$country_id." was not found",
			], 404);
		
		return Response::json($country, 200, [], JSON_NUMERIC_CHECK);
	}
}