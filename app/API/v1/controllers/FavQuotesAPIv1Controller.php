<?php

class FavQuotesAPIv1Controller extends BaseController {
	
	public function postFavorite($quote_id)
	{
		$user = User::find(ResourceServer::getOwnerId());

		$validatorQuote = Validator::make(compact('quote_id'), ['quote_id' => FavoriteQuote::$rulesAddFavorite['quote_id']]);
		if ($validatorQuote->fails()) {
			$data = [
				'status' => 'quote_not_found',
				'error'  => "The quote #".$quote_id." was not found",
			];

			return Response::json($data, 400);
		}

		// Try to find if the user has this quote in favorite from cache
		if (Cache::has(FavoriteQuote::$cacheNameFavoritesForUser.$user->id))
			$alreadyFavorited = in_array($quote_id, Cache::get(FavoriteQuote::$cacheNameFavoritesForUser.$user->id));
		else {
			$favorite = FavoriteQuote::where('quote_id', '=' , $quote_id)->where('user_id', '=' , $user->id)->count();
			$alreadyFavorited = ($favorite == 1);
		}

		if ($alreadyFavorited) {
			$data = [
				'status' => 'quote_already_favorited',
				'error'  => "The quote #".$quote_id." was already favorited",
			];

			return Response::json($data, 400);
		}

		// Store the favorite
		$favorite = new FavoriteQuote;
		$favorite->user_id = $user->id;
		$favorite->quote_id = $quote_id;
		$favorite->save();

		// Delete the cache
		if (Cache::has(FavoriteQuote::$cacheNameFavoritesForUser.$user->id))
			Cache::forget(FavoriteQuote::$cacheNameFavoritesForUser.$user->id);

		// If we have the number of favorites in cache, increment it
		if (Cache::has(Quote::$cacheNameNbFavorites.$quote_id))
			Cache::increment(Quote::$cacheNameNbFavorites.$quote_id);

		return Response::json($favorite, 200);
	}

	public function deleteFavorite($quote_id)
	{
		$user = User::find(ResourceServer::getOwnerId());

		$validatorFavoriteQuote = Validator::make(compact('quote_id'), ['quote_id' => 'exists:favorite_quotes,quote_id,user_id,'.$user->id]);
		if ($validatorFavoriteQuote->fails()) {
			$data = [
				'status' => 'quote_not_found',
				'error'  => "The quote #".$quote_id." was not found",
			];

			return Response::json($data, 400);
		}

		// Delete the FavoriteQuote from database
		FavoriteQuote::where('quote_id', '=' , $quote_id)->where('user_id', '=' , $user->id)->delete();

		// Delete the cache
		if (Cache::has(FavoriteQuote::$cacheNameFavoritesForUser.$user->id))
			Cache::forget(FavoriteQuote::$cacheNameFavoritesForUser.$user->id);

		// If we have the number of favorites in cache, decrement it
		if (Cache::has(Quote::$cacheNameNbFavorites.$quote_id))
			Cache::decrement(Quote::$cacheNameNbFavorites.$quote_id);

		$data = [
			'status' => 'favorite_deleted',
			'success'  => "The quote #".$quote_id." was deleted from favorites",
		];

		return Response::json($data, 200);
	}
}