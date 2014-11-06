<?php namespace TeenQuotes\Quotes\Models;

use Eloquent;
use TeenQuotes\Quotes\Models\Relations\FavoriteQuoteTrait as FavoriteQuoteRelationsTrait;
use TeenQuotes\Quotes\Models\Scopes\FavoriteQuoteTrait as FavoriteQuoteScopesTrait;

class FavoriteQuote extends Eloquent {

	use FavoriteQuoteRelationsTrait, FavoriteQuoteScopesTrait;
	
	protected $table = 'favorite_quotes';

	protected $fillable = [];

	public static $cacheNameFavoritesForUser = 'favorites_quotes_for_user_';
}