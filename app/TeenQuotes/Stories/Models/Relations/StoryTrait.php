<?php namespace TeenQuotes\Stories\Models\Relations;

trait StoryTrait {
	
	public function user()
	{
		return $this->belongsTo('User', 'user_id', 'id');
	}
}