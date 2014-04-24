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

	public function user()
	{
		return $this->belongsTo('User');
	}

	public function quote()
	{
		return $this->belongsTo('Quote');
	}
}