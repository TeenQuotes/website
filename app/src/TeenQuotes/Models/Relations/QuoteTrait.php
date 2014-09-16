<?php namespace TeenQuotes\Models\Relations;

trait QuoteTrait {

	public function user()
	{
		return $this->belongsTo('User', 'user_id', 'id');
	}

	public function comments()
	{
		return $this->hasMany('Comment');
	}

	public function favorites()
	{
		return $this->hasMany('FavoriteQuote')->orderBy('id', 'DESC');
	}
}