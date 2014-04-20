<?php

class Quote extends Eloquent {
	protected $fillable = [];
	/**
	 * Adding customs attributes to the object
	 * @var array
	 */
	protected $appends = array('has_favorites', 'total_favorites', 'has_comments', 'total_comments', 'is_favorite_for_current_user');

	/**
	 * The validation rules
	 * @var array
	 */
	public static $rules = [
		'content' => 'required|min:50|max:300',
		'user_id' => 'required|exists:users,id',
		'approved' => 'between:-1,2',
	]; 

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

	public function getHasFavoritesAttribute()
	{
		return ($this->total_favorites > 0);
	}

	public function getTotalFavoritesAttribute()
	{
		return $this->hasMany('FavoriteQuote')->count();    
	}

	public function getHasCommentsAttribute()
	{
		return ($this->total_comments > 0);
	}

	public function getIsFavoriteForCurrentUserAttribute()
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