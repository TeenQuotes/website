<?php namespace TeenQuotes\Users\Models\Relations;

trait UserTrait {

	public function comments()
	{
		return $this->hasMany('TeenQuotes\Comments\Models\Comment');
	}

	public function countryObject()
	{
		return $this->belongsTo('TeenQuotes\Countries\Models\Country', 'country', 'id');
	}

	public function newsletters()
	{
		return $this->hasMany('TeenQuotes\Newsletters\Models\Newsletter');
	}

	public function quotes()
	{
		return $this->hasMany('TeenQuotes\Quotes\Models\Quote');
	}

	public function settings()
	{
		return $this->hasMany('TeenQuotes\Settings\Models\Setting');
	}

	public function stories()
	{
		return $this->hasMany('TeenQuotes\Stories\Models\Story');
	}

	public function visitors()
	{
		return $this->belongsToMany('TeenQuotes\Users\Models\User', 'profile_visitors', 'visitor_id', 'user_id')->withTimestamps();
	}

	public function visited()
	{
		return $this->belongsToMany('TeenQuotes\Users\Models\User', 'profile_visitors', 'user_id', 'visitor_id')->withTimestamps();
	}

	public function favoriteQuotes()
	{
		return $this->belongsToMany('TeenQuotes\Quotes\Models\Quote', 'favorite_quotes')
			->with('user')
			->orderBy('favorite_quotes.id', 'DESC');
	}
}