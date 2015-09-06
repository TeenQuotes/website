<?php

/*
 * This file is part of the Teen Quotes website.
 *
 * (c) Antoine Augusti <antoine.augusti@teen-quotes.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TeenQuotes\Quotes;

use Illuminate\Support\ServiceProvider;
use TeenQuotes\Quotes\Repositories\CachingFavoriteQuoteRepository;
use TeenQuotes\Quotes\Repositories\CachingQuoteRepository;
use TeenQuotes\Quotes\Repositories\DbFavoriteQuoteRepository;
use TeenQuotes\Quotes\Repositories\DbQuoteRepository;
use TeenQuotes\Quotes\Repositories\FavoriteQuoteRepository;
use TeenQuotes\Quotes\Repositories\QuoteRepository;
use TeenQuotes\Tools\Namespaces\NamespaceTrait;

class QuotesServiceProvider extends ServiceProvider
{
    use NamespaceTrait;

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     */
    public function boot()
    {
        //
    }

    /**
     * Register the service provider.
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
        $this->registerQuotesCommands();

        // Search
        $this->registerSearchRoutes();
        $this->registerSearchComposers();
    }

    private function registerQuotesCommands()
    {
        $commands = [
            'quotesPublish' => 'QuotesPublishCommand',
            'quotesWaiting' => 'WaitingQuotesCommand',
        ];

        foreach ($commands as $key => $class) {
            $commandName = $this->getBaseNamespace().'Console\\'.$class;

            $this->app->bindShared('quotes.console.'.$key, function ($app) use ($commandName) {
                return $app->make($commandName);
            });

            $this->commands('quotes.console.'.$key);
        }
    }

    private function registerFavoriteQuoteBindings()
    {
        $this->app->bind(FavoriteQuoteRepository::class, function () {
            $eloquentRepo = new DbFavoriteQuoteRepository();

            return new CachingFavoriteQuoteRepository($eloquentRepo);
        });
    }

    private function registerQuotesBindings()
    {
        $this->app->bind(QuoteRepository::class, function () {
            $eloquentRepo = new DbQuoteRepository();

            return new CachingQuoteRepository($eloquentRepo);
        });
    }

    private function registerFavoriteQuoteRoutes()
    {
        $controller = 'FavoriteQuoteController';

        $this->app['router']->group($this->getRouteGroupParams(), function () use ($controller) {
            $this->app['router']->post('favorite/{quote_id}', ['as' => 'favorite', 'before' => 'auth', 'uses' => $controller.'@store']);
            $this->app['router']->post('unfavorite/{quote_id}', ['as' => 'unfavorite', 'before' => 'auth', 'uses' => $controller.'@destroy']);
        });
    }

    private function registerQuoteRoutes()
    {
        $controller = 'QuotesController';

        $this->app['router']->group($this->getRouteGroupParams(), function () use ($controller) {
            $this->app['router']->get('/', ['as' => 'home', 'uses' => $controller.'@index']);
            $this->app['router']->get('tags/{tag_name}', ['as' => 'quotes.tags.index', 'uses' => $controller.'@indexForTag']);
            $this->app['router']->get('top', ['as' => 'quotes.top', 'uses' => $controller.'@redirectTop']);
            $this->app['router']->get('top/favorites', ['as' => 'quotes.top.favorites', 'uses' => $controller.'@topFavorites']);
            $this->app['router']->get('top/comments', ['as' => 'quotes.top.comments', 'uses' => $controller.'@topComments']);
            $this->app['router']->get('random', ['as' => 'random', 'uses' => $controller.'@random']);
            $this->app['router']->get('addquote', ['as' => 'addquote', 'before' => 'auth', 'uses' => $controller.'@create']);
            $this->app['router']->get('quote-{quote_id}', ['uses' => $controller.'@redirectOldUrl']);
            $this->app['router']->post('quotes/favorites-info', ['as' => 'quotes.favoritesInfo', 'uses' => $controller.'@getDataFavoritesInfo']);
            $this->app['router']->resource('quotes', $controller, ['only' => ['index', 'show', 'store']]);
        });
    }

    private function registerSearchRoutes()
    {
        $controller = 'SearchController';

        $this->app['router']->group($this->getRouteGroupParams(), function () use ($controller) {
            $this->app['router']->get('search', ['as' => 'search.form', 'uses' => $controller.'@showForm']);
            $this->app['router']->post('search', ['as' => 'search.dispatcher', 'uses' => $controller.'@dispatcher']);
            $this->app['router']->get('search/{query}', ['as' => 'search.results', 'uses' => $controller.'@getResults']);
        });
    }

    private function registerQuotesComposers()
    {
        $composersNamespace = $this->getNamespaceComposers();

        // When indexing quotes
        $this->app['view']->composer([
            'quotes.partials.multiple',
        ], $composersNamespace.'IndexComposer');

        $this->app['view']->composer([
            'quotes.tags.index',
        ], $composersNamespace.'IndexForTagComposer');

        $this->app['view']->composer([
            'quotes.top.comments',
            'quotes.top.favorites',
        ], $composersNamespace.'IndexForTopsComposer');

        // When adding a quote
        $this->app['view']->composer([
            'quotes.addquote',
        ], $composersNamespace.'AddComposer');

        // When adding a comment on a single quote
        $this->app['view']->composer([
            'quotes.show',
        ], $composersNamespace.'ShowComposer');

        // View a single quote
        $this->app['view']->composer([
            'quotes.partials.singleQuote',
        ], $composersNamespace.'SingleComposer');

        // For deeps link
        $this->app['view']->composer([
            'quotes.index',
            'quotes.addquote',
        ], 'TeenQuotes\Tools\Composers\DeepLinksComposer');
    }

    private function registerSearchComposers()
    {
        // When showing search results
        $this->app['view']->composer([
            'search.results',
        ], $this->getNamespaceComposers().'ResultsComposer');
    }

    /**
     * Parameters for the group of routes.
     *
     * @return array
     */
    private function getRouteGroupParams()
    {
        return [
            'domain'    => $this->app['config']->get('app.domain'),
            'namespace' => $this->getBaseNamespace().'Controllers',
        ];
    }
}
