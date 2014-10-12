<?php namespace TeenQuotes\Quotes\Models\Relations;

trait QuoteTrait {

	public function user()
	{
		return $this->belongsTo('TeenQuotes\Users\Models\User', 'user_id', 'id');
	}

	public function comments()
	{
		return $this->hasMany('TeenQuotes\Comments\Models\Comment');
	}

	public function favorites()
	{
		return $this->hasMany('TeenQuotes\Quotes\Models\FavoriteQuote')->orderBy('id', 'DESC');
	}
}