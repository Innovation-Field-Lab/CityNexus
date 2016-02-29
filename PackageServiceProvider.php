<?php

namespace CityNexus\CityNexus;

use Illuminate\Support\ServiceProvider;

class CityNexus extends ServiceProvider
{

    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        if (! $this->app->routesAreCached()) {
            require __DIR__.'/../../routes.php';
        }

        $this->publishes([
            __DIR__.'/config.php' => config_path('citynexus.php'),
        ]);

        $this->publishes([
            __DIR__.'/assets' => public_path('vendor/citynexus'),
        ], 'public');

        $this->publishes([
            __DIR__.'/../database/migrations/' => database_path('migrations')
        ], 'migrations');
    }


    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->loadViewsFrom(__DIR__.'/views', 'citynexus');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }
}
