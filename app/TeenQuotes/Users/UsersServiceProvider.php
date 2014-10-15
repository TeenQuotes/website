<?php namespace TeenQuotes\Users;

use Illuminate\Support\ServiceProvider;
use TeenQuotes\Users\Models\User;
use TeenQuotes\Users\Observers\UserObserver;

class UsersServiceProvider extends ServiceProvider {

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
		$this->registerRoutes();
		$this->registerBindings();
		$this->registerViewComposers();
	}

	private function registerBindings()
	{
		$namespace = 'TeenQuotes\Users\Repositories';

		$this->app->bind(
			$namespace.'\UserRepository',
			$namespace.'\DbUserRepository'
		);
	}

	private function registerObserver()
	{
		User::observe(new UserObserver);
	}

	private function registerRoutes()
	{
		$this->app['router']->pattern('display_type', 'favorites|comments');
		
		$this->app['router']->group($this->getRouteGroupParams(), function() {
			$this->app['router']->delete('users', ['as' => 'users.delete', 'before' => 'auth', 'uses' => $this->getController().'@destroy']);
			$this->app['router']->get("signup", ["as" => "signup", "before" => "guest", "uses" => "UsersController@getSignup"]);
			$this->app['router']->get('users/{user_id}/{display_type?}', ['as' => 'users.show', 'uses' => $this->getController().'@show']);
			$this->app['router']->put('users/{user_id}/password', ['as' => 'users.password', 'uses' => $this->getController().'@putPassword']);
			$this->app['router']->put('users/{user_id}/avatar', ['as' => 'users.avatar', 'uses' => $this->getController().'@putAvatar']);
			$this->app['router']->put('users/{user_id}/settings', ['as' => 'users.settings', 'uses' => $this->getController().'@putSettings']);
			$this->app['router']->post('users/loginvalidator', ['as' => 'users.loginValidator', 'uses' => $this->getController().'@postLoginValidator']);
			$this->app['router']->resource('users', 'UsersController', ['only' => ['store', 'edit', 'update']]);
		});
	}

	private function registerViewComposers()
	{
		$namespace = 'TeenQuotes\Users\Composers';

		// When showing a user's profile
		$this->app['view']->composer([
			'users.show'
		], $namespace.'\ShowComposer');

		// Welcome page
		$this->app['view']->composer([
			'users.welcome'
		], $namespace.'\WelcomeComposer');

		// Self edit user's profile
		$this->app['view']->composer([
			'users.edit'
		], $namespace.'\ProfileEditComposer');
	}

	/**
	 * Parameters for the group of routes
	 * @return array
	 */
	private function getRouteGroupParams()
	{
		return [
			'domain'    => $this->app['config']->get('app.domain'),
			'namespace' => 'TeenQuotes\Users\Controllers',
		];
	}

	/**
	 * The controller name to handle requests
	 * @return string
	 */
	private function getController()
	{
		return 'UsersController';
	}
}