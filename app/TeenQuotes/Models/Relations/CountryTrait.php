<?php namespace TeenQuotes\Models\Relations;

trait CountryTrait {
	
	public function users()
	{
		return $this->hasMany('User', 'country', 'id');
	}
}