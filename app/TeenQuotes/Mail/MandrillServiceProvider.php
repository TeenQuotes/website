<?php namespace TeenQuotes\Mail;

use Illuminate\Support\ServiceProvider;

class MandrillServiceProvider extends ServiceProvider {
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
		$this->app->bind('mandrill', function()
		{
			$mandrillSecret = $this->app['config']->get('services.mandrill.secret');
			$mandrillAPI = new \Mandrill($mandrillSecret);

			return new Mandrill($mandrillAPI, $this->app->make('TeenQuotes\Users\Repositories\UserRepository'));
		});
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return ['mandrill'];
	}
}