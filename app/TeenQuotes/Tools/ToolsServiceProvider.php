<?php

namespace TeenQuotes\Tools;

use Illuminate\Support\ServiceProvider;
use TeenQuotes\Tools\Colors\ColorGenerator;
use TeenQuotes\Tools\Colors\ColorGeneratorInterface;

class ToolsServiceProvider extends ServiceProvider
{
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
    }

    private function registerBindings()
    {
        $this->app->bind(ColorGeneratorInterface::class, function () {
            return new ColorGenerator();
        });
    }
}
