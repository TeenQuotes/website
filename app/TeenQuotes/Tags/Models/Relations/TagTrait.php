<?php namespace TeenQuotes\Tags\Models\Relations;

use TeenQuotes\Quotes\Models\Quote;

trait TagTrait {

	public function quotes()
	{
		return $this->belongsToMany(Quote::class, 'quote_tag', 'tag_id', 'quote_id');
	}
}