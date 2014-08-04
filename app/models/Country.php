<?php

class Country extends \Eloquent {
	protected $table = 'countries';
	public $timestamps = false;
	protected $fillable = ['name'];

	/**
	 * The ID of the default country to use. This is the United States
	 * @var int
	 */
	const DEFAULT_COUNTRY = 224;

	public function users()
	{
		return $this->hasMany('User', 'country', 'id');
	}

	public static function getDefaultCountry()
	{
		return self::DEFAULT_COUNTRY;
	}
}