<?php namespace TeenQuotes\Settings;

use Illuminate\Support\ServiceProvider;
use TeenQuotes\Settings\Models\Setting;
use TeenQuotes\Settings\Observers\SettingObserver;

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
		$this->registerObserver();
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

	private function registerObserver()
	{
		Setting::observe(new SettingObserver);
	}

	private function registerBindings()
	{
		$namespace = 'TeenQuotes\Settings\Repositories';

		$this->app->bind(
			$namespace.'\SettingRepository',
			$namespace.'\DbSettingRepository'
		);
	}
}