<?php namespace TeenQuotes\Newsletters;

use Illuminate\Support\ServiceProvider;
use TeenQuotes\Tools\Namespaces\NamespaceTrait;

class NewslettersServiceProvider extends ServiceProvider {
	
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
		$namespace = $this->getBaseNamespace().'Repositories';
		
		$this->app->bind(
			$namespace.'\NewsletterRepository',
			$namespace.'\DbNewsletterRepository'
		);
	}

	private function registerCommands()
	{
		$commands = [
			'newsletters.console.sendNewsletter'           => $this->getNamespaceConsole().'SendNewsletterCommand',
			'newsletters.console.unsubscribeInactiveUsers' => $this->getNamespaceConsole().'UnsubscribeInactiveUsersCommand',
		];

		foreach ($commands as $key => $class)
		{
			$this->app->bindShared($key, function($app) use($class)
			{
				return $app->make($class);
			});

			$this->commands($key);
		}
	}
}