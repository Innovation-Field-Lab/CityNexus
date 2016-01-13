<?php

namespace Alaback\Todo;

use Illuminate\Support\ServiceProvider;

class TodoServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind('todo', function($app){
            return new Todo;
        });
    }

    public function boot()
    {
        // Load in the routes file
        require __DIR__ .  '/Http/routes.php';

        // Load views
        $this->loadViewsFrom(__DIR__ . '/../views', 'todo');

        // Define migrations to be published
        $this->publishes([
           __DIR__ . '/migrations/2015_10_01_000000_create_todo_table.php' =>
               base_path('database/migrations/2015_10_01_000000_create_todo_table.php')
        ]);
    }
}