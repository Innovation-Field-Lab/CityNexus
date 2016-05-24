<?php

namespace CityNexus\Tracker;

use Illuminate\Support\ServiceProvider;

class PackageServiceProvider extends ServiceProvider
{

    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        if (! $this->app->routesAreCached()) {
            require __DIR__ . '/routes.php';
        }

        $this->publishes([
            __DIR__ . '/config.php' => config_path('tracker.php'),
        ]);

        $this->publishes([
            __DIR__ . '/Public' => public_path('vendor/citynexus'),
        ], 'public');

        $this->publishes([
            __DIR__ . '/migrations/' => database_path('migrations')
        ], 'migrations');

        // Include Helpers
        include_once __DIR__ . '/helpers/Helper.php';


    }


    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->loadViewsFrom(__DIR__.'/views', 'tracker');


        $this->publishes([
            __DIR__.'/Public' => public_path('vendor/tracker'),
        ], 'public');


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
