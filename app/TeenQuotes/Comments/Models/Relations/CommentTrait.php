<?php namespace TeenQuotes\Comments\Models\Relations;

trait CommentTrait {

	public function user()
	{
		return $this->belongsTo('TeenQuotes\Users\Models\User');
	}

	public function quote()
	{
		return $this->belongsTo('TeenQuotes\Quotes\Models\Quote');
	}
}