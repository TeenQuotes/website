<?php

/*
 * This file is part of the Teen Quotes website.
 *
 * (c) Antoine Augusti <antoine.augusti@teen-quotes.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TeenQuotes\Robots;

use Illuminate\Support\ServiceProvider;
use TeenQuotes\Tools\Namespaces\NamespaceTrait;

class RobotsServiceProvider extends ServiceProvider
{
    use NamespaceTrait;

    /**
     * Register the service provider.
     */
    public function register()
    {
        $this->registerRoutes();
    }

    private function registerRoutes()
    {
        $controller = 'RobotsController';

        $this->app['router']->group($this->getRouteGroupParams(), function () use ($controller) {
            $this->app['router']->get('robots.txt', ['uses' => $controller.'@getRobots']);
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
            'namespace' => $this->getBaseNamespace().'Controllers',
        ];
    }
}
