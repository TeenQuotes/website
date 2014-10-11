<?php namespace TeenQuotes\Pages;

use Illuminate\Support\ServiceProvider;

class PagesServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = true;

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->registerAppsPages();
		$this->registerContactPage();
		$this->registerLegalPages();
	}

	private function registerAppsPages()
	{
		$this->app['router']->pattern('device_type', 'tablet|ios|android|mobile|desktop');

		$this->app['router']->group($this->getRouteGroupParams(), function() {
			$this->app['router']->get('apps', ['as' => 'apps', 'uses' => 'AppsController@index']);
			$this->app['router']->get('app', ['uses' => 'AppsController@redirectPlural']);
			$this->app['router']->get('apps/{device_type}', ['as' => 'apps.device', 'uses' => 'AppsController@getDevice']);
		});
	}

	private function registerContactPage()
	{
		$this->app['router']->group($this->getRouteGroupParams(), function() {
			$this->app['router']->get('contact', ['as' => 'contact', 'uses' => 'ContactController@index']);
		});
	}

	private function registerLegalPages()
	{
		$this->app['router']->pattern('legal_page', 'tos|privacy');
		
		$this->app['router']->group($this->getRouteGroupParams(), function() {
			$this->app['router']->get('legal/{legal_page?}', ['as' => 'legal.show', 'uses' => 'LegalController@show']);
		});
	}

	/**
	 * Parameters for the group of routes
	 * @return array
	 */
	private function getRouteGroupParams()
	{
		return [
			'domain'    => $this->app['config']->get('app.domain'),
			'namespace' => 'TeenQuotes\Pages\Controllers',
		];
	}
}