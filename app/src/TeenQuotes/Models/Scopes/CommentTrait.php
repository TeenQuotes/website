<?php namespace TeenQuotes\Models\Scopes;

trait CommentTrait {
	
	public function scopeOrderDescending($query)
	{
		return $query->orderBy('created_at', 'DESC');
	}
}