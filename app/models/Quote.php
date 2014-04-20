<?php

class Quote extends Eloquent {
	protected $fillable = [];
	/**
	* Adding customs attributes to the object
	*/
	protected $appends = array('total_favorites', 'total_comments', 'is_favorite');

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
		return $this->hasMany('FavoriteQuote');
	}

	public function getTotalCommentsAttribute()
	{
		return $this->hasMany('Comment')->count();    
	}

	public function getTotalFavoritesAttribute()
	{
		return $this->hasMany('FavoriteQuote')->count();    
	}

	public function getIsFavoriteAttribute()
	{
		if (Auth::check()) {
			$countFavorite = FavoriteQuote::where('quote_id', '=', $this->id)->where('user_id', '=', Auth::user()->id)->count();

			if ($countFavorite === 0)
				return false;
			else
				return true;
		}
		else
			return false;
	}

	public function scopeWaiting($query)
	{
		return $query->where('approved', '=', '0');
	}

	public function scopeRefused($query)
	{
		return $query->where('approved', '=', '-1');
	}

	public function scopePending($query)
	{
		return $query->where('approved', '=', '1');
	}

	public function scopePublished($query)
	{
		return $query->where('approved', '=', '2');
	}
}