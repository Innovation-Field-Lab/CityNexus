<?php

namespace CityNexus\CityNexus\Policies;

use CityNexus\CityNexus\Property;
use Illuminate\Auth\Access\HandlesAuthorization;

class DatasetPolicy
{
    use HandlesAuthorization;

    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        Property::class => DatasetPolicy::class,
    ];


    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Register any application authentication / authorization services.
     *
     * @param  \Illuminate\Contracts\Auth\Access\Gate  $gate
     * @return void
     */
    public function boot(GateContract $gate)
    {
        $this->registerPolicies($gate);



    }
}
