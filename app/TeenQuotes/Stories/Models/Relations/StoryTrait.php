<?php namespace TeenQuotes\Stories\Models\Relations;

trait StoryTrait {
	
	public function user()
	{
		return $this->belongsTo('TeenQuotes\Users\Models\User', 'user_id', 'id');
	}
}