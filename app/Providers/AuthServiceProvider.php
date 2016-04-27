<?php

namespace App\Providers;

use Illuminate\Contracts\Auth\Access\Gate as GateContract;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Auth;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\DatasetPolicy',
    ];

    /**
     * Register any application authentication / authorization services.
     *
     * @param  \Illuminate\Contracts\Auth\Access\Gate $gate
     * @return void
     */
    public function boot(GateContract $gate)
    {
        parent::registerPolicies($gate);


        $gate->before(function ($user) {
            if ($user->admin) {
                return true;
            }
        });

        // Dataset Permissions
        $gate->define('datasets', function($user, $method){
            return $user->allowed('datasets', $method);
        });

        // Scores Permissions
        $gate->define('scores', function($user, $method){
            return $user->allowed('scores', $method);
        });

    }
}
