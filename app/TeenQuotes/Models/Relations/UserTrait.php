<?php namespace TeenQuotes\Models\Relations;

trait UserTrait {
	
	public function comments()
	{
		return $this->hasMany('Comment');
	}

	public function countryObject()
	{
		return $this->belongsTo('Country', 'country', 'id');
	}

	public function newsletters()
	{
		return $this->hasMany('Newsletter');
	}

	public function quotes()
	{
		return $this->hasMany('Quote');
	}

	public function settings()
	{
		return $this->hasMany('Setting');
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