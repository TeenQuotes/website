<?php

namespace TeenQuotes\AdminPanel;

use Illuminate\Support\ServiceProvider;

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
        $this->app['router']->pattern('decision', 'approve|unapprove|alert');

        $this->app['router']->group($this->getRouteGroupParams(), function () {
            $this->app['router']->get('/', ['uses' => $this->getController().'@index', 'as' => 'admin.quotes.index']);
            $this->app['router']->get('edit/{quote_id}', ['uses' => $this->getController().'@edit', 'as' => 'admin.quotes.edit']);
            $this->app['router']->put('update/{quote_id}', ['uses' => $this->getController().'@update', 'as' => 'admin.quotes.update']);
            $this->app['router']->post('moderate/{quote_id}/{decision}', ['uses' => $this->getController().'@postModerate', 'as' => 'admin.quotes.moderate']);
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
