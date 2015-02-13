<?php namespace TeenQuotes\Comments\Models\Relations;

use TeenQuotes\Quotes\Models\Quote;
use TeenQuotes\Users\Models\User;

trait CommentTrait {

	public function user()
	{
		return $this->belongsTo(User::class);
	}

	public function quote()
	{
		return $this->belongsTo(Quote::class);
	}
}