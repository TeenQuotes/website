<?php namespace TeenQuotes\Users\Models\Scopes;

use Illuminate\Support\Facades\DB;

trait UserTrait {

	public function scopeBirthdayToday($query)
	{
		return $query->where(DB::raw("DATE_FORMAT(birthdate,'%m-%d')"), '=', DB::raw("DATE_FORMAT(NOW(),'%m-%d')"));
	}

	public function scopeNotHidden($query)
	{
		return $query->where('hide_profile', '=', 0);
	}

	public function scopeHidden($query)
	{
		return $query->where('hide_profile', '=', 1);
	}

	public function scopePartialLogin($query, $login)
	{
		return $query->whereRaw('login LIKE ?', ["%$login%"])->orderBy('login', 'ASC');
	}
}