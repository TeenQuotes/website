<?php namespace TeenQuotes\Countries\Presenters;

use Laracasts\Presenter\Presenter;

class CountryPresenter extends Presenter {

	/**
	 * The CSS class used to display the flag associated with
	 * the country
	 * @return string
	 */
	public function countryCodeClass()
	{
		$countryCode = strtolower($this->entity->country_code);

		return 'flag-'.$countryCode;
	}
}