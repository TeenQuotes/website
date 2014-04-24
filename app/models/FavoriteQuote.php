<?php

class FavoriteQuote extends Eloquent {
	protected $table = 'favorite_quotes';
	
	protected $fillable = [];

	public static $rulesFavorite = [
		'quote_id' => 'required|exists:quotes,id',
		'user_id' => 'required|exists:users,id',
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