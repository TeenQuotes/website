<?php namespace TeenQuotes\Quotes\Models\Scopes;

use Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

trait QuoteTrait {

	public function scopeCreatedToday($query)
	{
		return $query->whereBetween('created_at', [Carbon::today(), Carbon::today()->addDay()]);
	}

	public function scopeUpdatedToday($query)
	{
		return $query->whereBetween('updated_at', [Carbon::today(), Carbon::today()->addDay()]);
	}

	public function scopeWaiting($query)
	{
		return $query->where('approved', '=', self::WAITING);
	}

	public function scopeRefused($query)
	{
		return $query->where('approved', '=', self::REFUSED);
	}

	public function scopePending($query)
	{
		return $query->where('approved', '=', self::PENDING);
	}

	public function scopePublished($query)
	{
		return $query->where('approved', '=', self::PUBLISHED);
	}

	public function scopeForUser($query, $user)
	{
		return $query->where('user_id', '=', $user->id);
	}

	public function scopeOrderDescending($query)
	{
		return $query->orderBy('created_at', 'DESC');
	}

	public function scopeOrderAscending($query)
	{
		return $query->orderBy('created_at', 'ASC');
	}

	public function scopeRandom($query)
	{
		if (Config::get('database.default') != 'mysql')
			return $query;

		// Here we use a constant in the MySQL RAND function
		// so that quotes will be always be in the same "order"
		// even if they are not ordered
		// ref: http://dev.mysql.com/doc/refman/5.0/en/mathematical-functions.html#function_rand
		return $query->orderBy(DB::raw('RAND(42)'));
	}
}