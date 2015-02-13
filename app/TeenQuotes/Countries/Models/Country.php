<?php namespace TeenQuotes\Countries\Models;

use Eloquent, LaraSetting;
use TeenQuotes\Countries\Models\Relations\CountryTrait as CountryRelationsTrait;

class Country extends Eloquent {

	use CountryRelationsTrait;

	protected $table = 'countries';
	public $timestamps = false;
	protected $fillable = ['name'];

	/**
	 * The ID of the United States
	 * @var int
	 */
	const ID_UNITED_STATES = 224;

	public static function getDefaultCountry()
	{
		// If we have the information in the config file, return it
		if (LaraSetting::has('countries.defaultCountry'))
			return LaraSetting::get('countries.defaultCountry');

		// We have no clue, return the ID of the USA
		return self::ID_UNITED_STATES;
	}
}