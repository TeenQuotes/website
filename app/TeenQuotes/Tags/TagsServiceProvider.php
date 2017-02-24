<?php

/*
 * This file is part of the Teen Quotes website.
 *
 * (c) Antoine Augusti <antoine.augusti@teen-quotes.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TeenQuotes\Tags;

use Illuminate\Support\ServiceProvider;
use TeenQuotes\Tags\Repositories\CachingTagRepository;
use TeenQuotes\Tags\Repositories\DbTagRepository;
use TeenQuotes\Tags\Repositories\TagRepository;
use TeenQuotes\Tools\Namespaces\NamespaceTrait;

class TagsServiceProvider extends ServiceProvider
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
        $this->registerBindings();
        $this->registerCommands();
    }

    private function registerCommands()
    {
        $commands = [
            'tagQuotes' => 'TagQuotesCommand',
        ];

        foreach ($commands as $key => $class) {
            $commandName = $this->getBaseNamespace().'Console\\'.$class;

            $this->app->bindShared('tags.console.'.$key, function ($app) use ($commandName) {
                return $app->make($commandName);
            });

            $this->commands('tags.console.'.$key);
        }
    }

    private function registerBindings()
    {
        $this->app->bind(TagRepository::class, function () {
            return new CachingTagRepository(new DbTagRepository());
        });
    }
}
