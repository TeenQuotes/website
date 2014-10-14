<?php namespace TeenQuotes\Api\V1\Controllers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;
use TeenQuotes\Http\Facades\Response;
use TeenQuotes\Quotes\Models\FavoriteQuote;
use TeenQuotes\Quotes\Models\Quote;
use TeenQuotes\Quotes\Repositories\FavoriteQuoteRepository;
use TeenQuotes\Quotes\Repositories\QuoteRepository;
use TeenQuotes\Users\Models\User;

class QuotesFavoriteController extends APIGlobalController {
	
	/**
	 * @var TeenQuotes\Quotes\Repositories\FavoriteQuoteRepository
	 */
	private $favQuoteRepo;

	/**
	 * @var TeenQuotes\Quotes\Repositories\QuoteRepository
	 */
	private $quoteRepo;
	
	function __construct(FavoriteQuoteRepository $favQuoteRepo, QuoteRepository $quoteRepo)
	{
		$this->favQuoteRepo = $favQuoteRepo;
		$this->quoteRepo = $quoteRepo;
	}

	public function postFavorite($quote_id, $doValidation = true)
	{
		$user = $this->retrieveUser();
		
		if ($doValidation) {		
			
			$validatorQuote = Validator::make(compact('quote_id'), ['quote_id' => FavoriteQuote::$rulesAddFavorite['quote_id']]);
			if ($validatorQuote->fails())
				return Response::json([
					'status' => 'quote_not_found',
					'error'  => "The quote #".$quote_id." was not found.",
				], 400);

			// Check if the quote is published
			$quote = $this->quoteRepo->getById($quote_id);
			
			if ( ! $quote->isPublished())
				return Response::json([
					'status' => 'quote_not_published',
					'error'  => "The quote #".$quote_id." is not published.",
				], 400);

			// Try to find if the user has this quote in favorite from cache
			if (Cache::has(FavoriteQuote::$cacheNameFavoritesForUser.$user->id))
				$alreadyFavorited = in_array($quote_id, Cache::get(FavoriteQuote::$cacheNameFavoritesForUser.$user->id));
			else
				$alreadyFavorited = $this->favQuoteRepo->isFavoriteForUserAndQuote($user, $quote_id);

			if ($alreadyFavorited)
				return Response::json([
					'status' => 'quote_already_favorited',
					'error'  => "The quote #".$quote_id." was already favorited.",
				], 400);
		}

		// Store the favorite
		$favorite = $this->favQuoteRepo->create($user, $quote_id);

		// The cache flush will be handled by the observer

		return Response::json($favorite, 201, [], JSON_NUMERIC_CHECK);
	}

	public function deleteFavorite($quote_id, $doValidation = true)
	{
		$user = $this->retrieveUser();
		
		if ($doValidation) {		

			$validatorFavoriteQuote = Validator::make(compact('quote_id'), ['quote_id' => 'exists:favorite_quotes,quote_id,user_id,'.$user->id]);
			if ($validatorFavoriteQuote->fails())
				return Response::json([
					'status' => 'quote_not_found',
					'error'  => "The quote #".$quote_id." was not found.",
				], 400);
		}

		// Delete the FavoriteQuote from database
		$this->favQuoteRepo->deleteForUserAndQuote($user, $quote_id);

		return Response::json([
			'status'  => 'favorite_deleted',
			'success' => "The quote #".$quote_id." was deleted from favorites.",
		], 200, [], JSON_NUMERIC_CHECK);
	}
}