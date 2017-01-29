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
            __DIR__ . '/Public' => public_path('vendor/citynexus'),
        ], 'public');

        $this->publishes([
            __DIR__ . '/views/master' => base_path('resources/views/vendor/citynexus/master'),
        ], 'master');

        $this->publishes([
            __DIR__ . '/views/auth' => base_path('resources/views/vendor/citynexus/auth'),
        ], 'auth');

        $this->publishes([
            __DIR__ . '/migrations/' => database_path('migrations')
        ], 'migrations');


        // Include Helpers
        include_once __DIR__ . '/helpers/Helper.php';
        include_once __DIR__ . '/helpers/DatasetQuery.php';
        include_once __DIR__ . '/helpers/ScoreBuilder.php';
        include_once __DIR__ . '/helpers/Typer.php';
        include_once __DIR__ . '/helpers/TableBuilder.php';
        include_once __DIR__ . '/helpers/PropertySync.php';
        include_once __DIR__ . '/helpers/Dropbox.php';

        // Include Models
        include_once __DIR__ . '/models/Property.php';
        include_once __DIR__ . '/models/Score.php';
        include_once __DIR__ . '/models/Tag.php';
        include_once __DIR__ . '/models/Setting.php';
        include_once __DIR__ . '/models/Error.php';
        include_once __DIR__ . '/models/Note.php';
        include_once __DIR__ . '/models/Table.php';
        include_once __DIR__ . '/models/Upload.php';
        include_once __DIR__ . '/models/Location.php';
        include_once __DIR__ . '/models/View.php';
        include_once __DIR__ . '/models/Task.php';
        include_once __DIR__ . '/models/File.php';
        include_once __DIR__ . '/models/FileVersion.php';
        include_once __DIR__ . '/models/RawAddress.php';
        include_once __DIR__ . '/models/Widget.php';
        include_once __DIR__ . '/models/Uploader.php';
        include_once __DIR__ . '/models/Report.php';
        include_once __DIR__ . '/models/Export.php';

        // Include jobs
        include_once __DIR__ . '/Jobs/UploadData.php';
        include_once __DIR__ . '/Jobs/Geocode.php';
        include_once __DIR__ . '/Jobs/SendEmail.php';
        include_once __DIR__ . '/Jobs/MergeProps.php';
        include_once __DIR__ . '/Jobs/ProcessData.php';
        include_once __DIR__ . '/Jobs/CreateRaw.php';
        include_once __DIR__ . '/Jobs/BackUpTable.php';

        // Included Policies
        include_once __DIR__ . '/Policies/CityNexusPolicy.php';

        include_once __DIR__ . '/helpers/HelperFunctions.php';



    }


    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->loadViewsFrom(__DIR__.'/views', 'citynexus');


        $this->publishes([
            __DIR__.'/Public' => public_path('vendor/citynexus'),
        ], 'public');

    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
        ];
    }
}
