<?php namespace TeenQuotes\Api\V1\Controllers;

use TeenQuotes\Http\Facades\Response;

class CountriesController extends APIGlobalController {

	/**
	 * Show a single or all countries if we don't pass an ID
	 *
	 * @param  null|int $country_id
	 * @return \TeenQuotes\Http\Facades\Response
	 */
	public function show($country_id = null)
	{
		// List all countries
		if (is_null($country_id))
			return $this->listAll();

		// Get a single country
		$country = $this->countryRepo->findById($country_id);

		// Country not found
		if (is_null($country))
			return Response::json([
				'status' => 'country_not_found',
				'error'  => "The country #".$country_id." was not found.",
			], 404);

		return Response::json($country, 200, [], JSON_NUMERIC_CHECK);
	}

	/**
	 * List all countries
	 *
	 * @return \TeenQuotes\Http\Facades\Response
	 */
	private function listAll()
	{
		$countries = $this->countryRepo->getAll();

		return Response::json($countries, 200, [], JSON_NUMERIC_CHECK);
	}
}