<?php

/*
 * This file is part of the Teen Quotes website.
 *
 * (c) Antoine Augusti <antoine.augusti@teen-quotes.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TeenQuotes\Queues;

use Illuminate\Support\ServiceProvider;
use Queue;

class QueuesServiceProvider extends ServiceProvider
{
    /**
     * Register binding in IoC container.
     */
    public function register()
    {
        $this->registerRoutes();
    }

    public function registerRoutes()
    {
        $this->app['router']->group($this->getRouteGroupParams(), function () {
            $this->app['router']->post('queues/work', function () {
                return Queue::marshal();
            });
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
            'domain' => $this->app['config']->get('app.domain'),
        ];
    }
}
