<?php namespace TeenQuotes\Users\Models\Relations;

trait ProfileVisitorTrait {
	
	public function user()
	{
		return $this->belongsTo('TeenQuotes\Users\Models\User', 'user_id', 'id');
	}

	public function visitor()
	{
		return $this->belongsTo('TeenQuotes\Users\Models\User', 'visitor_id', 'id');
	}
}