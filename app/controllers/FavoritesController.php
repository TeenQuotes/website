<?php

class FavoritesController extends \BaseController {

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store($quote_id)
	{
		if (Request::ajax()) {

			$user = Auth::user();
			$data = [
				'quote_id' => $quote_id,
				'user_id'  => $user->id,
			];

			$validator = Validator::make($data, FavoriteQuote::$rulesAddFavorite);
			// FIXME : We can optimize because we will make a lot of queries
			// The validator will check for the existence of the user and will fetch the quote
			// These queries will be done 2 times
			$quote = Quote::find($data['quote_id']);

			// Check if the form validates with success.
			if ($validator->passes() AND ! is_null($quote) AND $quote->isPublished()) {

				// Try to find if the user has this quote in favorite from cache
				if (Cache::has(FavoriteQuote::$cacheNameFavoritesForUser.$data['user_id']))
					$alreadyFavorite = in_array($data['quote_id'], Cache::get(FavoriteQuote::$cacheNameFavoritesForUser.$data['user_id']));
				else {
					$favorite = FavoriteQuote::where('quote_id', '=' , $data['quote_id'])
						->where('user_id', '=' , $data['user_id'])
						->count();
					$alreadyFavorite = ($favorite === 1);
				}

				// Oops, the quote was already in its favorite
				if ($alreadyFavorite)
					return Response::json([
						'success'         => false,
						'alreadyFavorite' => true
					]);

				// Call the API to store the favorite
				$response = App::make('TeenQuotes\Api\V1\Controllers\FavQuotesController')->postFavorite($quote_id, false);
				
				if ($response->getStatusCode() == 201)
					return Response::json(['success' => true], 200);				
			}

			return Response::json([
				'success' => false, 
				'errors'  => $validator->getMessageBag()->toArray()
			]);
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
				$response = App::make('TeenQuotes\Api\V1\Controllers\FavQuotesController')->deleteFavorite($quote_id, false);
								
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