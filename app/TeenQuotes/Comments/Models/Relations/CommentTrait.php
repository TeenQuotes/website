<?php namespace TeenQuotes\Comments\Models\Relations;

trait CommentTrait {

	public function user()
	{
		return $this->belongsTo('User');
	}

	public function quote()
	{
		return $this->belongsTo('Quote');
	}
}