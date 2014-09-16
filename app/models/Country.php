<?php

class Country extends Eloquent {
	protected $table = 'countries';
	public $timestamps = false;
	protected $fillable = ['name'];

	/**
	 * The ID of the United States
	 */
	const ID_UNITED_STATES = 224;

	public function users()
	{
		return $this->hasMany('User', 'country', 'id');
	}

	public static function getDefaultCountry()
	{
		// If we have the information in settings.json, return it
		if (LaraSetting::has('countries.defaultCountry'))
			return LaraSetting::get('countries.defaultCountry');
		// We have no clue, return the ID of the USA
		else
			return self::ID_UNITED_STATES;
	}
}