<?php namespace TeenQuotes\Newsletters;

use Illuminate\Support\ServiceProvider;

class NewslettersServiceProvider extends ServiceProvider {

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
		$namespace = 'TeenQuotes\Newsletters\Repositories';
		
		$this->app->bind(
			$namespace.'\NewsletterRepository',
			$namespace.'\DbNewsletterRepository'
		);
	}
}