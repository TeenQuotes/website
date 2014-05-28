<?php

class APIGlobalController extends BaseController {

	public function showWelcome()
	{
		$data = [
			'status'            => 'You have arrived',
			'message'           => 'Welcome to the Teen Quotes API',
			'version'           => '1.0alpha',
			'url_documentation' => 'https://github.com/TeenQuotes/api-documentation',
			'contact'           => 'antoine.augusti@teen-quotes.com',
		];

		return Response::json($data, 200);
	}
}