<?php namespace TeenQuotes\Models\Relations;

trait ProfileVisitorTrait {
	
	public function user()
	{
		return $this->belongsTo('User', 'user_id', 'id');
	}

	public function visitor()
	{
		return $this->belongsTo('User', 'visitor_id', 'id');
	}
}