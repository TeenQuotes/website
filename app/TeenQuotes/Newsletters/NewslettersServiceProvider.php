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
		$this->registerWebhooks();
	}

	private function registerWebhooks()
	{
		$controller = 'MailchimpWebhook';

		$this->app['router']->group($this->getRouteGroupParams(), function() use ($controller) {
			$this->app['router']->get('mailchimp/webhook', ['as' => 'mailchimp.webhook', 'uses' => $controller.'@listen']);
		});
	}

	private function registerBindings()
	{
		$this->app->bind(
			$this->getNamespaceRepositories().'NewsletterRepository',
			$this->getNamespaceRepositories().'DbNewsletterRepository'
		);
	}

	private function registerCommands()
	{
		$commands = [
			'newsletters.console.sendNewsletter'   => $this->getNamespaceConsole().'SendNewsletterCommand',
			'newsletters.console.unsubscribeUsers' => $this->getNamespaceConsole().'UnsubscribeUsersCommand',
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

	/**
	 * Parameters for the group of routes
	 * @return array
	 */
	private function getRouteGroupParams()
	{
		return [
			'domain'    => $this->app['config']->get('app.domain'),
			'namespace' => $this->getBaseNamespace().'Controllers',
		];
	}
}