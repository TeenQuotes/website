<?php namespace TeenQuotes\Countries;

use Illuminate\Support\ServiceProvider;
use TeenQuotes\Countries\Repositories\CachingCountryRepository;
use TeenQuotes\Countries\Repositories\CountryRepository;
use TeenQuotes\Countries\Repositories\DbCountryRepository;
use TeenQuotes\Tools\Namespaces\NamespaceTrait;

class CountriesServiceProvider extends ServiceProvider {

	use NamespaceTrait;

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
		$this->app->bind(CountryRepository::class, function()
		{
			$eloquentRepo = new DbCountryRepository;

			return new CachingCountryRepository($eloquentRepo);
		});
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
}