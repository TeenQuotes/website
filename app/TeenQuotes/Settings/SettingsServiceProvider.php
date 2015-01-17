<?php namespace TeenQuotes\Settings;

use Illuminate\Support\ServiceProvider;
use TeenQuotes\Settings\Repositories\CachingSettingRepository;
use TeenQuotes\Settings\Repositories\DbSettingRepository;
use TeenQuotes\Settings\Repositories\SettingRepository;

class SettingsServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		//
	}

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
		$namespace = 'TeenQuotes\Settings\Repositories\\';

		$this->app->bind(SettingRepository::class, function() use ($namespace)
		{
			$eloquentRepo = new DbSettingRepository;

			return new CachingSettingRepository($eloquentRepo, $this->app->make('Illuminate\Cache\Repository'));
		});
	}
}