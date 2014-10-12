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
		//
	}

	private function registerObserver()
	{
		Setting::observe(new SettingObserver);
	}
}