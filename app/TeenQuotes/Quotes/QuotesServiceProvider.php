<?php namespace TeenQuotes\Quotes;

use Illuminate\Support\ServiceProvider;
use TeenQuotes\Quotes\Models\FavoriteQuote;
use TeenQuotes\Quotes\Observers\FavoriteQuoteObserver;

class QuotesServiceProvider extends ServiceProvider {

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
		$this->registerObservers();
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->registerFavoriteQuoteRoutes();
		$this->registerQuoteRoutes();
	}

	private function registerObservers()
	{
		FavoriteQuote::observe(new FavoriteQuoteObserver);
	}

	private function registerFavoriteQuoteRoutes()
	{
		$controller = 'FavoriteQuoteController';
		
		$this->app['router']->group($this->getRouteGroupParams(), function() use ($controller) {
			$this->app['router']->post('favorite/{quote_id}', ['as' => 'favorite', 'before' => 'auth', 'uses' => $controller.'@store']);
			$this->app['router']->post('unfavorite/{quote_id}', ['as' => 'unfavorite', 'before' => 'auth', 'uses' => $controller.'@destroy']);
		});
	}

	private function registerQuoteRoutes()
	{
		$controller = 'QuotesController';
		
		$this->app['router']->group($this->getRouteGroupParams(), function() use ($controller) {
			$this->app['router']->get('/', ['as' => 'home', 'uses' => $controller.'@index']);
			$this->app['router']->get('random', ['as' => 'random', 'uses' => $controller.'@index']);
			$this->app['router']->get('addquote', ['as' => 'addquote', 'before' => 'auth', 'uses' => $controller.'@create']);
			$this->app['router']->get('quote-{quote_id}', ['uses' => $controller.'@redirectOldUrl']);
			$this->app['router']->post('quotes/favorites-info', ['as' => 'quotes.favoritesInfo', 'uses' => $controller.'@getDataFavoritesInfo']);
			$this->app['router']->resource('quotes', $controller, ['only' => ['index', 'show', 'store']]);
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
			'namespace' => 'TeenQuotes\Quotes\Controllers',
		];
	}
}