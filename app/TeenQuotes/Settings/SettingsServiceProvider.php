<?php

namespace TeenQuotes\Settings;

use Illuminate\Support\ServiceProvider;
use TeenQuotes\Settings\Repositories\CachingSettingRepository;
use TeenQuotes\Settings\Repositories\DbSettingRepository;
use TeenQuotes\Settings\Repositories\SettingRepository;

class SettingsServiceProvider extends ServiceProvider
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
        $this->app->bind(SettingRepository::class, function () {
            $eloquentRepo = new DbSettingRepository();

            return new CachingSettingRepository($eloquentRepo, $this->app->make('Illuminate\Cache\Repository'));
        });
    }
}
