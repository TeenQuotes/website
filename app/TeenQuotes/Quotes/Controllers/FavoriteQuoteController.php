<?php namespace TeenQuotes\Quotes\Controllers;

use BaseController;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use TeenQuotes\Quotes\Models\FavoriteQuote;
use TeenQuotes\Quotes\Models\Quote;

class FavoriteQuoteController extends BaseController {

	/**
	 * The API controller
	 * @var TeenQuotes\Api\V1\Controllers\QuotesFavoriteController
	 */
	private $api;

	public function __construct()
	{
		$this->api = App::make('TeenQuotes\Api\V1\Controllers\QuotesFavoriteController');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store($quote_id)
	{
		if (Request::ajax()) {

			// Call the API to store the favorite
			$response = $this->api->postFavorite($quote_id);
			
			if ($response->getStatusCode() == 201)
				return Response::json(['success' => true], 200);

			return Response::json(['success' => false], 200);
		}
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($quote_id)
	{
		if (Request::ajax()) {

			$user = Auth::user();
			$data = [
				'quote_id' => $quote_id,
				'user_id'  => $user->id,
			];

			$validator = Validator::make($data, FavoriteQuote::$rulesRemoveFavorite);

			// Check if the form validates with success.
			if ($validator->passes()) {

				// Call the API to delete the favorite
				$response = $this->api->deleteFavorite($quote_id, false);
								
				if ($response->getStatusCode() == 200)
					return Response::json(['success' => true], 200);
			}

			return Response::json([
				'success' => false,
				'errors'  => $validator->getMessageBag()->toArray()
			]);
		}
	}
}