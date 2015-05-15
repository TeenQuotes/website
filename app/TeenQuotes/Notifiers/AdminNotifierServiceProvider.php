<?php

namespace TeenQuotes\Notifiers;

use Illuminate\Support\ServiceProvider;
use TeenQuotes\Notifiers\Pushbullet\PushbulletAdminNotifier;

class AdminNotifierServiceProvider extends ServiceProvider
{
    /**
     * Register binding in IoC container.
     */
    public function register()
    {
        $this->app->bind('TeenQuotes\Notifiers\AdminNotifier', function ($app) {
            $config = $this->app['config'];

            $lang = $this->app['translator'];
            $apiKey = $config->get('services.pushbullet.apiKey');
            $deviceIden = $config->get('services.pushbullet.deviceIden');

            return new PushbulletAdminNotifier($lang, $apiKey, $deviceIden);
        });
    }
}
