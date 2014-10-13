<?php namespace TeenQuotes\Countries;

use Illuminate\Support\ServiceProvider;

class CountriesServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->registerBindings();
	}

	private function registerBindings()
	{
		$namespace = 'TeenQuotes\Countries\Repositories';

		$this->app->bind(
			$namespace.'\CountryRepository',
			$namespace.'\DbCountryRepository'
		);
	}
}