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
		$this->registerObserver();
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		// FavoriteQuote
		$this->registerFavoriteQuoteRoutes();
		$this->registerFavoriteQuoteBindings();
		
		// Quotes
		$this->registerQuoteRoutes();
		$this->registerQuotesComposers();
		$this->registerQuotesBindings();

		// Search
		$this->registerSearchRoutes();
		$this->registerSearchComposers();
	}

	private function registerObserver()
	{
		FavoriteQuote::observe(new FavoriteQuoteObserver);
	}

	private function registerFavoriteQuoteBindings()
	{
		$namespace = 'TeenQuotes\Quotes\Repositories';

		$this->app->bind(
			$namespace.'\FavoriteQuoteRepository',
			$namespace.'\DbFavoriteQuoteRepository'
		);
	}

	private function registerQuotesBindings()
	{
		$namespace = 'TeenQuotes\Quotes\Repositories';

		$this->app->bind(
			$namespace.'\QuoteRepository',
			$namespace.'\DbQuoteRepository'
		);
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

	private function registerSearchRoutes()
	{
		$controller = 'SearchController';
		
		$this->app['router']->group($this->getRouteGroupParams(), function() use ($controller) {
			$this->app['router']->get('search', ['as' => 'search.form', 'uses' => $controller.'@showForm']);
			$this->app['router']->post('search', ['as' => 'search.dispatcher', 'uses' => $controller.'@dispatcher']);
			$this->app['router']->get('search/{query}', ['as' => 'search.results', 'uses' => $controller.'@getResults']);
		});
	}

	private function registerQuotesComposers()
	{
		// When indexing quotes
		$this->app['view']->composer([
			'quotes.index'
		], $this->getNamespaceComposers().'\IndexComposer');

		// When adding a quote
		$this->app['view']->composer([
			'quotes.addquote'
		], $this->getNamespaceComposers().'\AddComposer');

		// When adding a comment on a single quote
		$this->app['view']->composer([
			'quotes.show'
		], $this->getNamespaceComposers().'\ShowComposer');

		// View a single quote
		$this->app['view']->composer([
			'quotes.singleQuote'
		], $this->getNamespaceComposers().'\SingleComposer');

		// For deeps link
		$this->app['view']->composer([
			'quotes.index',
			'quotes.addquote'
		], 'TeenQuotes\Tools\Composers\DeepLinksComposer');
	}

	private function registerSearchComposers()
	{
		// When showing search results
		$this->app['view']->composer([
			'search.results'
		], $this->getNamespaceComposers().'\ResultsComposer');
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

	private function getNamespaceComposers()
	{
		return 'TeenQuotes\Quotes\Composers';
	}
}