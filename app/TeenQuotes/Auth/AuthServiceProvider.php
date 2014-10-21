<?php namespace TeenQuotes\Auth;

use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider {

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
		// Authentification
		$this->registerAuthRoutes();
		$this->registerAuthViewComposers();

		// Reminders
		$this->registerReminderRoutes();
		$this->registerReminderViewComposers();
	}

	private function registerAuthRoutes()
	{
		$controller = 'AuthController';

		$this->app['router']->group($this->getRouteGroupParams(), function() use ($controller) {
			$this->app['router']->get('signin', ['as' => 'signin', 'uses' => $controller.'@getSignin']);
			$this->app['router']->get('logout', ['as' => 'logout', 'uses' => $controller.'@getLogout']);
			$this->app['router']->post('signin', $controller.'@postSignin');
		});
	}

	private function registerAuthViewComposers()
	{
		// Send event to GA when not logged in
		$this->app['view']->composer([
			'auth.signin'
		], $this->getNamespaceComposers().'SigninComposer');

		// When signing up
		$this->app['view']->composer([
			'auth.signup'
		], $this->getNamespaceComposers().'SignupComposer');

		// For deeps link
		$this->app['view']->composer([
			'auth.signin',
			'auth.signup'
		], 'TeenQuotes\Tools\Composers\DeepLinksComposer');	
	}

	private function registerReminderRoutes()
	{
		$controller = 'RemindersController';

		$this->app['router']->group($this->getRouteGroupParams(), function() use ($controller) {
			$this->app['router']->get('password/remind', ['as' => 'password.remind', 'uses' => $controller.'@getRemind']);
			$this->app['router']->post('password/remind', ['as' => 'password.remind', 'uses' => $controller.'@postRemind']);
			$this->app['router']->get('password/reset', ['as' => 'password.reset', 'uses' => $controller.'@getReset']);
			$this->app['router']->post('password/reset', ['as' => 'password.reset',  'uses' => $controller.'@postReset']);
		});
	}

	private function registerReminderViewComposers()
	{
		// Reset a password with a token
		$this->app['view']->composer([
			'password.reset'
		], $this->getNamespaceComposers().'ResetComposer');
		
		// For deeps link
		$this->app['view']->composer([
			'password.remind'
		], 'TeenQuotes\Tools\Composers\DeepLinksComposer');	
	}

	/**
	 * Parameters for the group of routes
	 * @return array
	 */
	private function getRouteGroupParams()
	{
		return [
			'domain'    => $this->app['config']->get('app.domain'),
			'namespace' => 'TeenQuotes\Auth\Controllers'
		];
	}

	private function getNamespaceComposers()
	{
		return 'TeenQuotes\Auth\Composers\\';
	}
}