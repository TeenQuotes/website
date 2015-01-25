<?php namespace TeenQuotes\Countries\Repositories;

use Cache;

class CachingCountryRepository implements CountryRepository {

	/**
	 * @var \TeenQuotes\Countries\Repositories\CountryRepository
	 */
	private $countries;

	public function __construct(CountryRepository $countries)
	{
		$this->countries = $countries;
	}

	/**
	 * @see \TeenQuotes\Countries\Repositories\CountryRepository
	 */
	public function findById($id)
	{
		$keyName = $this->getCacheKeyNameForId($id);

		return Cache::rememberForever($keyName, function() use($id)
		{
			return $this->countries->findById($id);
		});
	}

	/**
	 * @see \TeenQuotes\Countries\Repositories\CountryRepository
	 */
	public function listNameAndId()
	{
		return Cache::rememberForever('countries.all.list', function()
		{
			return $this->countries->listNameAndId();
		});
	}

	/**
	 * @see \TeenQuotes\Countries\Repositories\CountryRepository
	 */
	public function getAll()
	{
		return Cache::rememberForever('countries.all', function()
		{
			return $this->countries->getAll();
		});
	}

	private function getCacheKeyNameForId($id)
	{
		return 'countries.'.$id;
	}
}