<?php
namespace TeenQuotes\Database;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Toloquent extends Eloquent {

	/**
	 * Gets a model small user's info
	 * @param  Query $query The original query
	 * @param  string $source Where we can access the user's info. Default to 'user'
	 * @return Query The modified query
	 */
	public function scopeWithSmallUser($query, $source = 'user')
	{
		return $query->with(array($source => function($q)
		{
		    $q->addSelect(array('id', 'login', 'avatar'));
		}));
	}
}