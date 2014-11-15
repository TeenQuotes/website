<?php namespace TeenQuotes\Api\V1;

use Illuminate\Support\ServiceProvider;

class ApiServiceProvider extends ServiceProvider {

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->registerErrorHandlers();
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->registerOAuthRoutes();
		$this->registerRoutesPatterns();

		$this->registerCommentsRoutes();
		$this->registerCountriesRoutes();
		$this->registerFavoriteQuotesRoutes();
		$this->registerPasswordRoutes();
		$this->registerQuotesRoutes();
		$this->registerSearchRoutes();
		$this->registerStoriesRoutes();
		$this->registerUsersRoutes();
	}

	private function registerErrorHandlers()
	{
		$this->app->error(function(\TeenQuotes\Exceptions\ApiNotFoundException $exception, $code)
		{
			$status = 404;
			$error = 'No '.$exception->getMessage().' have been found.';
			
			return \TeenQuotes\Http\Facades\Response::json(compact('status', 'error'), 404);
		});
	}

	private function registerRoutesPatterns()
	{
		$this->app['router']->pattern('quote_id', '[0-9]+');
		$this->app['router']->pattern('country_id', '[0-9]+');
		$this->app['router']->pattern('story_id', '[0-9]+');
		$this->app['router']->pattern('comment_id', '[0-9]+');
		$this->app['router']->pattern('user_id', '[a-zA-Z0-9_]+');
		$this->app['router']->pattern('quote_approved_type', 'waiting|refused|pending|published');
		$this->app['router']->pattern('random', 'random');
	}

	private function registerCommentsRoutes()
	{
		$this->app['router']->group($this->getRouteGroupParams(), function() {
			$this->app['router']->get('comments/{quote_id}', ['uses' => 'CommentsController@index']);
			$this->app['router']->put('comments/{comment_id}', ['uses' => 'CommentsController@update']);
			$this->app['router']->post('comments/{quote_id}', ['uses' => 'CommentsController@store']);
			$this->app['router']->delete('comments/{comment_id}', ['uses' => 'CommentsController@destroy']);
			$this->app['router']->get('comments/{comment_id}', ['uses' => 'CommentsController@show']);
			$this->app['router']->get('comments/users/{user_id}', 'CommentsController@getCommentsForUser');
		});
	}

	private function registerCountriesRoutes()
	{
		$this->app['router']->group($this->getRouteGroupParams(), function() {
			$this->app['router']->get('countries/{country_id?}', ['uses' => 'CountriesController@show']);
		});
	}

	private function registerFavoriteQuotesRoutes()
	{
		$this->app['router']->group($this->getRouteGroupParams(), function() {
			$this->app['router']->post('favorites/{quote_id}', ['uses' => 'QuotesFavoriteController@postFavorite']);
			$this->app['router']->delete('favorites/{quote_id}', ['uses' => 'QuotesFavoriteController@deleteFavorite']);
		});
	}

	private function registerPasswordRoutes()
	{		
		$this->app['router']->group($this->getRouteGroupParams(), function() {
			$this->app['router']->post('password/remind', ['uses' => 'PasswordController@postRemind']);
			$this->app['router']->post('password/reset', ['uses' => 'PasswordController@postReset']);
		});
	}

	private function registerQuotesRoutes()
	{
		$this->app['router']->group($this->getRouteGroupParams(), function() {
			$this->app['router']->post('quotes', ['uses' => 'QuotesController@store']);
			$this->app['router']->get('quotes/{quote_id}', ['uses' => 'QuotesController@show']);
			$this->app['router']->get('quotes/{random?}', ['uses' => 'QuotesController@index']);
			$this->app['router']->get('quotes/favorites/{user_id?}', ['uses' => 'QuotesController@indexFavoritesQuotes']);
			$this->app['router']->get('quotes/{quote_approved_type}/{user_id}', ['uses' => 'QuotesController@indexByApprovedQuotes']);
			$this->app['router']->get('quotes/search/{query}', ['uses' => 'QuotesController@getSearch']);
		});
	}

	private function registerUsersRoutes()
	{
		$this->app['router']->group($this->getRouteGroupParams(), function() {
			$this->app['router']->delete('users',['uses' => 'UsersController@destroy']);
			$this->app['router']->post('users', ['uses' => 'UsersController@store']);
			$this->app['router']->get('users', ['uses' => 'UsersController@getUsers']);
			$this->app['router']->put('users/profile', ['uses' => 'UsersController@putProfile']);
			$this->app['router']->get('users/{user_id}', ['uses' => 'UsersController@show']);
			$this->app['router']->put('users/password', ['uses' => 'UsersController@putPassword']);
			$this->app['router']->put('users/settings', ['uses' => 'UsersController@putSettings']);
			$this->app['router']->get('users/search/{query}', ['uses' => 'UsersController@getSearch']);
		});
	}
				
	private function registerSearchRoutes()
	{
		$this->app['router']->group($this->getRouteGroupParams(), function() {
			$this->app['router']->get('search/{query}', ['uses' => 'SearchController@getSearch']);
		});
	}

	private function registerStoriesRoutes()
	{
		$this->app['router']->group($this->getRouteGroupParams(), function() {
			$this->app['router']->get('stories', ['uses' => 'StoriesController@index']);
			$this->app['router']->post('stories', ['uses' => 'StoriesController@store']);
			$this->app['router']->get('stories/{story_id}', ['uses' => 'StoriesController@show']);
		});
	}

	private function registerOAuthRoutes()
	{
		$routeGroupParams = $this->getRouteGroupParams();
		// Disable OAuth filter
		$routeGroupParams['before'] = 'session.remove';
		// No prefix
		array_forget($routeGroupParams, 'prefix');
		
		$this->app['router']->group($routeGroupParams, function() {
			$this->app['router']->post('oauth', ['uses' => 'APIGlobalController@postOauth']);
			$this->app['router']->get('/', ['uses' => 'APIGlobalController@showWelcome']);
		});
	}

	/**
	 * Get the key value parameters for the group of routes
	 * @return array
	 */
	private function getRouteGroupParams()
	{
		return [
			'domain'    => $this->app['config']->get('app.domainAPI'),
			'before'    => 'oauth|session.remove', 
			'prefix'    => 'v1', 
			'namespace' => 'TeenQuotes\Api\V1\Controllers'
		];
	}
}