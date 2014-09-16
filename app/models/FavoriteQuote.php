<?php

use TeenQuotes\Models\Relations\FavoriteQuoteTrait as FavoriteQuoteRelationsTrait;
use TeenQuotes\Models\Scopes\FavoriteQuoteTrait as FavoriteQuoteScopesTrait;

class FavoriteQuote extends Eloquent {

	use FavoriteQuoteRelationsTrait, FavoriteQuoteScopesTrait;
	
	protected $table = 'favorite_quotes';

	protected $fillable = [];

	/**
	 * The validation rules when adding a favorite quote
	 * @var array
	 */
	public static $rulesAddFavorite = [
		'quote_id' => 'required|exists:quotes,id',
		'user_id' => 'required|exists:users,id',
	];

	/**
	 * The validation rules when deleting a favorite quote
	 * @var array
	 */
	public static $rulesRemoveFavorite = [
		'quote_id' => 'required|exists:quotes,id|exists:favorite_quotes,quote_id',
		'user_id' => 'required|exists:users,id|exists:favorite_quotes,user_id',
	];

	public static $cacheNameFavoritesForUser = 'favorites_quotes_for_user_';
}