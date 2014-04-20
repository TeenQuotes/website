<?php

class FavoriteQuote extends Eloquent {
	protected $table = 'favorite_quotes';
	
	protected $fillable = [];

	public function user()
	{
		return $this->belongsTo('User');
	}

	public function quote()
	{
		return $this->belongsTo('Quote');
	}
}