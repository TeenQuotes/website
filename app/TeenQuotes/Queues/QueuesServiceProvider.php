<?php namespace TeenQuotes\Queues;

use Queue;
use Illuminate\Support\ServiceProvider;

class QueuesServiceProvider extends ServiceProvider {

	/**
	 * Register binding in IoC container
	 */
	public function register()
	{
		$this->registerRoutes();
	}

	public function registerRoutes()
	{
		$this->app['router']->group($this->getRouteGroupParams(), function() {
			$this->app['router']->post('queues/work', function()
			{
				return Queue::marshal();
			});
		});
	}

	/**
	 * Parameters for the group of routes
	 * @return array
	 */
	private function getRouteGroupParams()
	{
		return [
			'domain' => $this->app['config']->get('app.domain'),
		];
	}
}