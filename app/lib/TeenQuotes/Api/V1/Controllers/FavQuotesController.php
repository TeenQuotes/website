<?php
namespace TeenQuotes\Api\V1\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use LucaDegasperi\OAuth2Server\Facades\ResourceServerFacade as ResourceServer;
use \FavoriteQuote;
use \User;
use \Quote;

class FavQuotesController extends APIGlobalController {
	
	public function postFavorite($quote_id, $doValidation = true)
	{
		$user = ResourceServer::getOwnerId() ? User::find(ResourceServer::getOwnerId()) : Auth::user();

		if ($doValidation) {		
			$validatorQuote = Validator::make(compact('quote_id'), ['quote_id' => FavoriteQuote::$rulesAddFavorite['quote_id']]);
			if ($validatorQuote->fails()) {
				$data = [
					'status' => 'quote_not_found',
					'error'  => "The quote #".$quote_id." was not found.",
				];

				return Response::json($data, 400);
			}

			// Check if the quote is published
			$quote = Quote::find($quote_id);
			if (!$quote->isPublished()) {
				$data = [
					'status' => 'quote_not_published',
					'error'  => "The quote #".$quote_id." is not published.",
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
					'error'  => "The quote #".$quote_id." was already favorited.",
				];

				return Response::json($data, 400);
			}
		}

		// Store the favorite
		$favorite = new FavoriteQuote;
		$favorite->user_id = $user->id;
		$favorite->quote_id = $quote_id;
		$favorite->save();

		// Delete the cache
		if (Cache::has(FavoriteQuote::$cacheNameFavoritesForUser.$user->id))
			Cache::forget(FavoriteQuote::$cacheNameFavoritesForUser.$user->id);

		// Delete favorite quotes stored in cache
		$nbFavorites = count($user->arrayIDFavoritesQuotes());
		$nbPages = ceil($nbFavorites / Config::get('app.users.nbQuotesPerPage'));
		for ($i = 1; $i <= $nbPages; $i++)
			Cache::forget(User::$cacheNameForFavorited.$user->id.'_'.$i);

		// If we have the number of favorites in cache, increment it
		if (Cache::has(Quote::$cacheNameNbFavorites.$quote_id))
			Cache::increment(Quote::$cacheNameNbFavorites.$quote_id);

		return Response::json($favorite, 200, [], JSON_NUMERIC_CHECK);
	}

	public function deleteFavorite($quote_id, $doValidation = true)
	{
		$user = ResourceServer::getOwnerId() ? User::find(ResourceServer::getOwnerId()) : Auth::user();

		if ($doValidation) {		
			$validatorFavoriteQuote = Validator::make(compact('quote_id'), ['quote_id' => 'exists:favorite_quotes,quote_id,user_id,'.$user->id]);
			if ($validatorFavoriteQuote->fails()) {
				$data = [
					'status' => 'quote_not_found',
					'error'  => "The quote #".$quote_id." was not found",
				];

				return Response::json($data, 400);
			}
		}

		// Delete the FavoriteQuote from database
		FavoriteQuote::where('quote_id', '=' , $quote_id)->where('user_id', '=' , $user->id)->delete();

		// Delete the cache
		if (Cache::has(FavoriteQuote::$cacheNameFavoritesForUser.$user->id))
			Cache::forget(FavoriteQuote::$cacheNameFavoritesForUser.$user->id);

		// If we have the number of favorites in cache, decrement it
		if (Cache::has(Quote::$cacheNameNbFavorites.$quote_id))
			Cache::decrement(Quote::$cacheNameNbFavorites.$quote_id);

		// Rebuild the cache
		$arrayIDFavoritesQuotesForUser = $user->arrayIDFavoritesQuotes();

		// Delete favorite quotes stored in cache
		$nbQuotesFavoriteForUser = count($arrayIDFavoritesQuotesForUser) + 1;
		$nbPages = ceil($nbQuotesFavoriteForUser / Config::get('app.users.nbQuotesPerPage'));
		for ($i = 1; $i <= $nbPages ; $i++)
			Cache::forget(User::$cacheNameForFavorited.$user->id.'_'.$i);

		$data = [
			'status' => 'favorite_deleted',
			'success'  => "The quote #".$quote_id." was deleted from favorites",
		];

		return Response::json($data, 200, [], JSON_NUMERIC_CHECK);
	}
}