<?php namespace TeenQuotes\Models\Relations;

trait StoryTrait {
	
	public function user()
	{
		return $this->belongsTo('User', 'user_id', 'id');
	}
}