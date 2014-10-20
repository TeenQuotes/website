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
		$this->registerCommands();
	}

	private function registerBindings()
	{
		$namespace = $this->getBaseNamespace().'Repositories';

		$this->app->bind(
			$namespace.'\CountryRepository',
			$namespace.'\DbCountryRepository'
		);
	}

	private function registerCommands()
	{
		$commandName = $this->getBaseNamespace().'Console\MostCommonCountryCommand';

		$this->app->bindShared('countries.console.mostCommonCountry', function($app) use($commandName)
		{
			return $app->make($commandName);
		});

		$this->commands('countries.console.mostCommonCountry');
	}

	private function getBaseNamespace()
	{
		return 'TeenQuotes\Countries\\';
	}
}