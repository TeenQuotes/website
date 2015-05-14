<?php namespace TeenQuotes\Users;

use Illuminate\Support\ServiceProvider;
use TeenQuotes\Tools\Namespaces\NamespaceTrait;
use TeenQuotes\Users\Models\User;
use TeenQuotes\Users\Observers\UserObserver;

class UsersServiceProvider extends ServiceProvider {

	use NamespaceTrait;
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
		$this->registerSearchRoutes();
		$this->registerBindings();
		$this->registerViewComposers();
		$this->registerCommands();
	}

	private function registerBindings()
	{
		$repos = ['User', 'ProfileVisitor'];

		foreach ($repos as $repo)
		{
			$this->app->bind(
				$this->getNamespaceRepositories().$repo.'Repository',
				$this->getNamespaceRepositories().'Db'.$repo.'Repository'
			);
		}
	}

	private function registerObserver()
	{
		User::observe(new UserObserver);
	}

	private function registerRoutes()
	{
		$this->app['router']->pattern('display_type', 'favorites|comments');

		$controller = $this->getController();

		$this->app['router']->group($this->getRouteGroupParams(), function() use ($controller)
		{
			$this->app['router']->get('user-{user_id}', ['uses' => $controller.'@redirectOldUrl'])->where('user_id', '[0-9]+');
			$this->app['router']->get('users/{user_id}/{display_type?}', ['as' => 'users.show', 'uses' => $controller.'@show']);
			$this->app['router']->any('users/{wildcard}', $this->getController().'@notFound');
		});

		$this->app['router']->group($this->getRouteGroupParamsAccount(), function() use ($controller)
		{
			$this->app['router']->get('signup', ['as' => 'signup', 'before' => 'guest', 'uses' => $controller.'@getSignup']);
			$this->app['router']->delete('users', ['as' => 'users.delete', 'before' => 'auth', 'uses' => $controller.'@destroy']);
			$this->app['router']->post('users/loginvalidator', ['as' => 'users.loginValidator', 'uses' => $controller.'@postLoginValidator']);
			$this->app['router']->put('users/{user_id}/password', ['as' => 'users.password', 'uses' => $controller.'@putPassword']);
			$this->app['router']->put('users/{user_id}/avatar', ['as' => 'users.avatar', 'uses' => $controller.'@putAvatar']);
			$this->app['router']->put('users/{user_id}/settings', ['as' => 'users.settings', 'uses' => $controller.'@putSettings']);
			$this->app['router']->resource('users', $controller, ['only' => ['store', 'edit', 'update']]);
		});
	}

	private function registerSearchRoutes()
	{
		$controller = 'SearchController';
		$routeGroup = $this->getRouteGroupParams();
		$routeGroup['namespace'] = 'TeenQuotes\Quotes\Controllers';

		$this->app['router']->group($routeGroup, function() use ($controller) {
			$this->app['router']->get('search/users/country/{country_id}', ['as' => 'search.users.country', 'uses' => $controller.'@usersFromCountry']);
		});
	}

	private function registerViewComposers()
	{
		$namespace = $this->getBaseNamespace().'Composers';

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

		// Search users coming from a given country
		$this->app['view']->composer([
			'search.users'
		], $namespace.'\SearchUsersCountryComposer');
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

	/**
	 * Get parameters for the account section
	 * @return array
	 */
	private function getRouteGroupParamsAccount()
	{
		$data = $this->getRouteGroupParams();
		// Switch to the secure domain
		$data['domain'] = $this->app['config']->get('app.domainAccount');

		return $data;
	}

	private function registerCommands()
	{
		// Send birthday
		$commandName = $this->getBaseNamespace().'Console\SendBirthdayCommand';
		$this->app->bindShared('users.console.sendBirthday', function($app) use($commandName)
		{
			return $app->make($commandName);
		});

		$this->commands('users.console.sendBirthday');

		// Send special event
		$commandName = $this->getBaseNamespace().'Console\EmailSpecialEventCommand';
		$this->app->bindShared('users.console.emailSpecialEvent', function($app) use($commandName)
		{
			return $app->make($commandName);
		});

		$this->commands('users.console.emailSpecialEvent');
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
