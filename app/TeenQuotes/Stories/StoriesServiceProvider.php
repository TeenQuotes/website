<?php

/*
 * This file is part of the Teen Quotes website.
 *
 * (c) Antoine Augusti <antoine.augusti@teen-quotes.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TeenQuotes\Stories;

use Illuminate\Support\ServiceProvider;
use TeenQuotes\Tools\Namespaces\NamespaceTrait;

class StoriesServiceProvider extends ServiceProvider
{
    use NamespaceTrait;

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
        $this->registerBindings();
    }

    private function registerRoutes()
    {
        $this->app['router']->group($this->getRouteGroupParams(), function () {
            $this->app['router']->get('/', ['as' => 'stories', 'uses' => $this->getController().'@index']);
            $this->app['router']->get('story/{story_id}', ['as' => 'story.show', 'uses' => $this->getController().'@show']);
            $this->app['router']->post('story/new', ['as' => 'story.store', 'before' => 'auth', 'uses' => $this->getController().'@store']);
        });
    }

    private function registerBindings()
    {
        $namespace = $this->getNamespaceRepositories();

        $this->app->bind(
            $namespace.'StoryRepository',
            $namespace.'DbStoryRepository'
        );
    }

    /**
     * Parameters for the group of routes.
     *
     * @return array
     */
    private function getRouteGroupParams()
    {
        return [
            'domain'    => $this->app['config']->get('app.domainStories'),
            'namespace' => $this->getBaseNamespace().'Controllers',
        ];
    }

    /**
     * The controller name to handle requests.
     *
     * @return string
     */
    private function getController()
    {
        return 'StoriesController';
    }
}
