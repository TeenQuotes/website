<?php namespace TeenQuotes\Countries\Models\Relations;

trait CountryTrait {
	
	public function users()
	{
		return $this->hasMany('TeenQuotes\Users\Models\User', 'country', 'id');
	}
}