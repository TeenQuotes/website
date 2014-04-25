<?php

class FavoriteQuote extends Eloquent {
	protected $table = 'favorite_quotes';
	
	protected $fillable = [];

	public static $rulesAddFavorite = [
		'quote_id' => 'required|exists:quotes,id',
		'user_id' => 'required|exists:users,id',
	];

	public static $rulesRemoveFavorite = [
		'quote_id' => 'required|exists:quotes,id|exists:favorite_quotes,quote_id',
		'user_id' => 'required|exists:users,id|exists:favorite_quotes,user_id',
	];

	public static $cacheNameFavoritesForUser = 'favorites_quotes_for_user_';

	public function user()
	{
		return $this->belongsTo('User');
	}

	public function quote()
	{
		return $this->belongsTo('Quote');
	}

	public function scopeCurrentUser($query)
	{
		if (!Auth::check())
			throw new NotAllowedException("Can't get favorites quotes for a guest user!");

		return $query->where('user_id', '=', Auth::id());
	}
}