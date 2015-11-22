<?php

/*
 * This file is part of the Teen Quotes website.
 *
 * (c) Antoine Augusti <antoine.augusti@teen-quotes.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TeenQuotes\AdminPanel;

use Illuminate\Support\ServiceProvider;
use TeenQuotes\AdminPanel\Helpers\Moderation;

class AdminPanelServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Register the service provider.
     */
    public function register()
    {
        $this->registerRoutes();
        $this->registerViewComposers();
    }

    private function registerRoutes()
    {
        $this->app['router']->pattern('decision', Moderation::presentAvailableTypes());

        $controller = $this->getController();

        $this->app['router']->group($this->getRouteGroupParams(), function () use ($controller) {
            $this->app['router']->get('/', ['uses' => $controller.'@index', 'as' => 'admin.quotes.index']);
            $this->app['router']->get('edit/{quote_id}', ['uses' => $controller.'@edit', 'as' => 'admin.quotes.edit']);
            $this->app['router']->put('update/{quote_id}', ['uses' => $controller.'@update', 'as' => 'admin.quotes.update']);
            $this->app['router']->post('moderate/{quote_id}/{decision}', ['uses' => $controller.'@postModerate', 'as' => 'admin.quotes.moderate']);
        });
    }

    private function registerViewComposers()
    {
        // JS variables used when moderating quotes
        $this->app['view']->composer([
            'admin.index',
        ], 'TeenQuotes\AdminPanel\Composers\ModerationIndexComposer');
    }

    /**
     * Parameters for the group of routes.
     *
     * @return array
     */
    private function getRouteGroupParams()
    {
        return [
            'domain'    => $this->app['config']->get('app.domainAdmin'),
            'namespace' => 'TeenQuotes\AdminPanel\Controllers',
            'before'    => 'admin',
        ];
    }

    /**
     * The controller name to handle requests.
     *
     * @return string
     */
    private function getController()
    {
        return 'QuotesAdminController';
    }
}
