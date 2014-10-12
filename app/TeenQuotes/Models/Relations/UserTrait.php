<?php namespace TeenQuotes\Models\Relations;

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
		return $this->hasMany('Quote');
	}

	public function settings()
	{
		return $this->hasMany('TeenQuotes\Settings\Models\Setting');
	}

	public function stories()
	{
		return $this->hasMany('TeenQuotes\Stories\Models\Story');
	}

	public function usersVisitors()
	{
		return $this->hasMany('ProfileVisitor', 'user_id', 'id');
	}

	public function usersVisited()
	{
		return $this->hasMany('ProfileVisitor', 'visitor_id', 'id');
	}

	public function favoriteQuotes()
	{
		return $this->belongsToMany('Quote', 'favorite_quotes')
			->with('user')
			->orderBy('favorite_quotes.id', 'DESC');
	}
}