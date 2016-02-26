<?php

/*
 * This file is part of the Teen Quotes website.
 *
 * (c) Antoine Augusti <antoine.augusti@teen-quotes.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TeenQuotes\Newsletters;

use Illuminate\Support\ServiceProvider;
use TeenQuotes\Tools\Namespaces\NamespaceTrait;

class NewslettersServiceProvider extends ServiceProvider
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
        $this->registerBindings();
        $this->registerCommands();
        $this->registerWebhooks();
    }

    private function registerWebhooks()
    {
        $controller = 'MailchimpWebhook';

        $this->app['router']->group($this->getRouteGroupParams(), function () use ($controller) {
            $this->app['router']->match(['GET', 'POST'], 'mailchimp/webhook', ['as' => 'mailchimp.webhook', 'uses' => $controller.'@listen']);
        });
    }

    private function registerBindings()
    {
        $this->app->bind(
            $this->getNamespaceRepositories().'NewsletterRepository',
            $this->getNamespaceRepositories().'DbNewsletterRepository'
        );
    }

    private function registerCommands()
    {
        $commands = [
            'newsletters.console.sendNewsletter'   => $this->getNamespaceConsole().'SendNewsletterCommand',
        ];

        foreach ($commands as $key => $class) {
            $this->app->bindShared($key, function ($app) use ($class) {
                return $app->make($class);
            });

            $this->commands($key);
        }
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
