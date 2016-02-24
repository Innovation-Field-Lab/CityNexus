<?php

namespace CityNexus\CityNexus;

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
            __DIR__ . '/config.php' => config_path('citynexus.php'),
        ]);

        $this->publishes([
            __DIR__ . '/assets' => public_path('vendor/citynexus'),
        ], 'public');

        $this->publishes([
            __DIR__ . '/migrations/' => database_path('migrations')
        ], 'migrations');

        require __DIR__ . '/helpers/DatasetQuery.php';
        require __DIR__ . '/helpers/ScoreBuilder.php';
        require __DIR__ . '/Jobs/GenerateScore.php';
        require __DIR__ . '/models/Property.php';
        require __DIR__ . '/models/Score.php';
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
