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

			// Check if the form validates with success.
			if ($validator->passes()) {
				
				// Try to find if the user has a this quote in favorite from cache
				if (Cache::has(FavoriteQuote::$cacheNameFavoritesForUser.$data['user_id']))
					$alreadyFavorite = in_array($data['quote_id'], Cache::get(FavoriteQuote::$cacheNameFavoritesForUser.$data['user_id']));
				else {
					$favorite = FavoriteQuote::where('quote_id', '=' , $data['quote_id'])->where('user_id', '=' , $data['user_id'])->first();
					$alreadyFavorite = !is_null($favorite);
				}
								
				// Oops, the quote was already in its favorite
				if ($alreadyFavorite) {
					return Response::json(['success' => false, 'alreadyFavorite' => true]);
				}
				else {
					// Store the favorite
					$favorite = new FavoriteQuote;
					$favorite->user_id = $data['user_id'];
					$favorite->quote_id = $data['quote_id'];
					$favorite->save();

					// Delete the cache
					if (Cache::has(FavoriteQuote::$cacheNameFavoritesForUser.$data['user_id']))
						Cache::forget(FavoriteQuote::$cacheNameFavoritesForUser.$data['user_id']);

					return Response::json(['success' => true], 200);
				}
				
			}
			// Errors
			else
				return Response::json(['success' => false, 'errors' => $validator->getMessageBag()->toArray()]);
		}
		// It was not an Ajax call
		// FIXME: what to do here?
		else
			return Redirect::route('route');
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
				
				// Try to find if the user has a this quote in favorite from cache
				if (Cache::has(FavoriteQuote::$cacheNameFavoritesForUser.$data['user_id']))
					$quoteIsFavorite = in_array($data['quote_id'], Cache::get(FavoriteQuote::$cacheNameFavoritesForUser.$data['user_id']));
				else {
					$favorite = FavoriteQuote::where('quote_id', '=' , $data['quote_id'])->where('user_id', '=' , $data['user_id'])->first();
					$quoteIsFavorite = !is_null($favorite);
				}

				// Oops, the quote was not labeled as favorite
				if (!$quoteIsFavorite) {
					return Response::json(['success' => false, 'notFound' => true]);
				}
				else {
					
					// Delete the FavoriteQuote from database
					FavoriteQuote::where('quote_id', '=' , $data['quote_id'])->where('user_id', '=' , $data['user_id'])->delete();

					// Delete the cache
					if (Cache::has(FavoriteQuote::$cacheNameFavoritesForUser.$data['user_id']))
						Cache::forget(FavoriteQuote::$cacheNameFavoritesForUser.$data['user_id']);

					return Response::json(['success' => true], 200);
				}
				
			}
			// Errors
			else
				return Response::json(['success' => false, 'errors' => $validator->getMessageBag()->toArray()]);
		}
		// It was not an Ajax call
		// FIXME: what to do here?
		else
			return Redirect::route('route');
	}
}