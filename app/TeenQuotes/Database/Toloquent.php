<?php
namespace TeenQuotes\Database;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Toloquent extends Eloquent {

	/**
	 * Get a model small user's info
	 *
	 * @param  \Illuminate\Database\Eloquent\Builder $query The original query
	 * @param  string $source Where we can access the user's info. Default to 'user'
	 * @return \Illuminate\Database\Query\Builder The modified query
	 */
	public function scopeWithSmallUser($query, $source = 'user')
	{
		return $query->with([$source => function($q)
		{
		    $q->addSelect(['id', 'login', 'avatar', 'hide_profile']);
		}]);
	}
}