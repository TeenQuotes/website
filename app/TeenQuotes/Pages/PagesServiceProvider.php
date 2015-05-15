<?php

namespace TeenQuotes\Pages;

use Illuminate\Support\ServiceProvider;

class PagesServiceProvider extends ServiceProvider
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
        // Apps
        $this->registerAppsPages();
        $this->registerAppsViewComposers();

        // Contact
        $this->registerContactPage();
        $this->registerContactViewComposers();

        // Legal
        $this->registerLegalPages();
        $this->registerLegalViewComposers();
    }

    private function registerAppsPages()
    {
        $this->app['router']->pattern('device_type', 'tablet|ios|android|mobile|desktop');

        $this->app['router']->group($this->getRouteGroupParams(), function () {
            $this->app['router']->get('apps', ['as' => 'apps', 'uses' => 'AppsController@index']);
            $this->app['router']->get('app', ['uses' => 'AppsController@redirectPlural']);
            $this->app['router']->get('apps/{device_type}', ['as' => 'apps.device', 'uses' => 'AppsController@getDevice']);
        });
    }

    private function registerAppsViewComposers()
    {
        // Apps download page
        $this->app['view']->composer([
            'apps.download',
        ], 'TeenQuotes\Pages\Composers\AppsComposer');

        // For deeps link
        $this->app['view']->composer([
            'apps.download',
        ], 'TeenQuotes\Tools\Composers\DeepLinksComposer');
    }

    private function registerContactViewComposers()
    {
        // For deeps link
        $this->app['view']->composer([
            'contact.show',
        ], 'TeenQuotes\Tools\Composers\DeepLinksComposer');
    }

    private function registerLegalViewComposers()
    {
        // For deeps link
        $this->app['view']->composer([
            'legal.show',
        ], 'TeenQuotes\Tools\Composers\DeepLinksComposer');
    }

    private function registerContactPage()
    {
        $this->app['router']->group($this->getRouteGroupParams(), function () {
            $this->app['router']->get('contact', ['as' => 'contact', 'uses' => 'ContactController@index']);
        });
    }

    private function registerLegalPages()
    {
        $this->app['router']->pattern('legal_page', 'tos|privacy');

        $this->app['router']->group($this->getRouteGroupParams(), function () {
            $this->app['router']->get('legal/{legal_page?}', ['as' => 'legal.show', 'uses' => 'LegalController@show']);
        });
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
            'namespace' => 'TeenQuotes\Pages\Controllers',
        ];
    }
}
