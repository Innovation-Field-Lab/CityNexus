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

        include_once __DIR__ . '/helpers/DatasetQuery.php';
        include_once __DIR__ . '/helpers/ScoreBuilder.php';
        include_once __DIR__ . '/helpers/Typer.php';
        include_once __DIR__ . '/helpers/TableBuilder.php';
        include_once __DIR__ . '/Jobs/GenerateScore.php';
        include_once __DIR__ . '/models/Property.php';
        include_once __DIR__ . '/models/Score.php';
        include_once __DIR__ . '/models/Setting.php';
        include_once __DIR__ . '/models/Error.php';
        include_once __DIR__ . '/models/Note.php';
        include_once __DIR__ . '/models/Table.php';
        include_once __DIR__ . '/models/Upload.php';
        include_once __DIR__ . '/Jobs/UploadData.php';
        include_once __DIR__ . '/Jobs/Geocode.php';
        include_once __DIR__ . '/Jobs/InviteUser.php';

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
