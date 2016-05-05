<?php

namespace CityNexus\CityNexus\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use App\User;

class CityNexusPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    public function datasets(User $user, $method)
    {
        return $user->allowed('datasets', $method);
    }

    public function scores(User $user, $method)
    {
        return $user->allowed('scores', $method);
    }

    public function usersAdmin(User $user, $method)
    {
        return $user->allowed('usersAdmin', $method);
    }

    public function properties(User $user, $method)
    {
        return $user->allowed('peroperties', $method);
    }

    public function reports(User $user, $method)
    {
        return $user->allowed('reports', $method);
    }

    public function admin(User $user, $method)
    {
        return $user->allowed('admin', $method);
    }
}
