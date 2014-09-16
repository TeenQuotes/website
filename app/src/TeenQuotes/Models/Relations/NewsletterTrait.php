<?php namespace TeenQuotes\Models\Relations;

trait NewsletterTrait {

	public function user()
	{
		return $this->belongsTo('User');
	}
}