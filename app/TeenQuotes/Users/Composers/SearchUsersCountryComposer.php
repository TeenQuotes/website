<?php namespace TeenQuotes\Users\Composers;

use Lang;

class SearchUsersCountryComposer {

	/**
	 * Add data to a view
	 * @param  \Illuminate\View\View $view
	 */
	public function compose($view)
	{
		$data = $view->getData();
		$country = $data['country'];
		$countryName = $country->name;

		$view->with('pageTitle', Lang::get('search.usersCountryPageTitle', compact('countryName')));
		$view->with('pageDescription', Lang::get('search.usersCountryPageDescription', compact('countryName')));
	}
}