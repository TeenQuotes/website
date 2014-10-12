<?php namespace TeenQuotes\Newsletters\Models\Relations;

trait NewsletterTrait {

	public function user()
	{
		return $this->belongsTo('TeenQuotes\Users\Models\User');
	}
}