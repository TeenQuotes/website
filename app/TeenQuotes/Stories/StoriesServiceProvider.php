<?php namespace TeenQuotes\Stories;

use Illuminate\Support\ServiceProvider;

class StoriesServiceProvider extends ServiceProvider {

	private $controller = 'StoriesController';

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app['router']->group($this->getRouteGroupParams(), function() {
			$this->app['router']->get('/', ['as' => 'stories', 'uses' => $this->controller.'@index']);
			$this->app['router']->get('story/{story_id}', ['as' => 'story.show', 'uses' => $this->controller.'@show']);
			$this->app['router']->post('story/new', ['as' => 'story.store', 'before' => 'auth', 'uses' => $this->controller.'@store']);
		});
	}

	private function getRouteGroupParams()
	{
		return [
			'domain' => $this->app['config']->get('app.domainStories')
		];
	}
}