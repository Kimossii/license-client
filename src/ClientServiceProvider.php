<?php

namespace LicenseClient;

use Illuminate\Support\ServiceProvider;

class ClientServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'license-client');
        $this->publishes([
            __DIR__ . '/../config/license.php' => config_path('license-client.php'),
        ]);
         $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/license.php',
            'license-client'
        );
    }
}
