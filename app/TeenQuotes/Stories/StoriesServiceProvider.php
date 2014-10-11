<?php namespace TeenQuotes\Stories;

use Illuminate\Support\ServiceProvider;

class StoriesServiceProvider extends ServiceProvider {

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->registerRoutes();
	}

	private function registerRoutes()
	{
		$this->app['router']->group($this->getRouteGroupParams(), function() {
			$this->app['router']->get('/', ['as' => 'stories', 'uses' => $this->getController().'@index']);
			$this->app['router']->get('story/{story_id}', ['as' => 'story.show', 'uses' => $this->getController().'@show']);
			$this->app['router']->post('story/new', ['as' => 'story.store', 'before' => 'auth', 'uses' => $this->getController().'@store']);
		});
	}

	/**
	 * Parameters for the group of routes
	 * @return array
	 */
	private function getRouteGroupParams()
	{
		return [
			'domain' => $this->app['config']->get('app.domainStories')
		];
	}

	/**
	 * The controller name to handle requests
	 * @return string
	 */
	private function getController()
	{
		return 'TeenQuotes\Stories\Controllers\StoriesController';
	}
}