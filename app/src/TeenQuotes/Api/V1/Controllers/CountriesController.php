<?php namespace TeenQuotes\Api\V1\Controllers;

use Country;
use Illuminate\Support\Facades\Response;

class CountriesController extends APIGlobalController {
	
	public function getCountry($country_id = null)
	{
		// List all countries
		if (is_null($country_id))
			return Response::json(Country::all(), 200, [], JSON_NUMERIC_CHECK);
		
		// Get a single country
		$country = Country::find($country_id);

		// Country not found
		if (is_null($country))
			return Response::json([
				'status' => 'country_not_found',
				'error'  => "The country #".$country_id." was not found",
			], 404);
		
		return Response::json($country, 200, [], JSON_NUMERIC_CHECK);
	}
}