<?php namespace TeenQuotes\Models\Scopes;

trait CommentTrait {
	
	public function scopeOrderDescending($query)
	{
		return $query->orderBy('created_at', 'DESC');
	}

	public function scopeForQuoteId($query, $id)
	{
		return $query->where('quote_id', '=', $id);
	}

	public function scopeForQuote($query, Quote $q)
	{
		return $query->where('quote_id', '=', $q->id);
	}
}