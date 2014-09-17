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
			
			if ($validatorQuote->fails())
				return Response::json([
					'status' => 'quote_not_found',
					'error'  => "The quote #".$quote_id." was not found.",
				], 400);

			// Check if the quote is published
			$quote = Quote::find($quote_id);
			
			if ( ! $quote->isPublished())
				return Response::json([
					'status' => 'quote_not_published',
					'error'  => "The quote #".$quote_id." is not published.",
				], 400);

			// Try to find if the user has this quote in favorite from cache
			if (Cache::has(FavoriteQuote::$cacheNameFavoritesForUser.$user->id))
				$alreadyFavorited = in_array($quote_id, Cache::get(FavoriteQuote::$cacheNameFavoritesForUser.$user->id));
			else {
				$favorite = FavoriteQuote::where('quote_id', '=' , $quote_id)
					->where('user_id', '=' , $user->id)
					->count();
				$alreadyFavorited = ($favorite == 1);
			}

			if ($alreadyFavorited)
				return Response::json([
					'status' => 'quote_already_favorited',
					'error'  => "The quote #".$quote_id." was already favorited.",
				], 400);
		}

		// Store the favorite
		$favorite = new FavoriteQuote;
		$favorite->user_id = $user->id;
		$favorite->quote_id = $quote_id;
		$favorite->save();

		// The cache flush will be handled by the observer

		return Response::json($favorite, 200, [], JSON_NUMERIC_CHECK);
	}

	public function deleteFavorite($quote_id, $doValidation = true)
	{
		$user = ResourceServer::getOwnerId() ? User::find(ResourceServer::getOwnerId()) : Auth::user();

		if ($doValidation) {		

			$validatorFavoriteQuote = Validator::make(compact('quote_id'), ['quote_id' => 'exists:favorite_quotes,quote_id,user_id,'.$user->id]);
			if ($validatorFavoriteQuote->fails())
				return Response::json([
					'status' => 'quote_not_found',
					'error'  => "The quote #".$quote_id." was not found",
				], 400);
		}

		// Delete the FavoriteQuote from database
		// We can't chain all our methods, otherwise the deleted event
		// will not be fired
		$fav = FavoriteQuote::where('quote_id', '=' , $quote_id)
			->where('user_id', '=' , $user->id)->first();
		$fav->delete();

		return Response::json([
			'status'  => 'favorite_deleted',
			'success' => "The quote #".$quote_id." was deleted from favorites",
		], 200, [], JSON_NUMERIC_CHECK);
	}
}