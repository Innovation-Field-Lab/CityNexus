<?php

namespace App;

use CityNexus\CityNexus\Widget;
use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class User extends Model implements AuthenticatableContract,
                                    AuthorizableContract,
                                    CanResetPasswordContract
{
    use Authenticatable, Authorizable, CanResetPassword, SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['first_name', 'last_name', 'email', 'super_admin', 'permissions', 'password'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];

    public function fullname()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function allowed($set, $permission)
    {
        $permissions = json_decode($this->permissions);
        if(isset($permissions->$set->$permission) && $permissions->$set->$permission) return true;
        else return false;
    }
    public function disallowed($set, $permission)
    {
        $permissions = json_decode($this->permissions);
        if(!isset($permissions->$set->$permission) or !$permissions->$set->$permission) return true;
        else return false;

    }

    public function getWidgetsAttribute()
    {
        if($this->dashboard)
        {
            $widgets = json_decode($this->dashboard);
        }
        else
        {

            $widgets = json_decode(setting('globalDashboard'));
        }

        $widgets = Widget::findMany($widgets);
        return $widgets;
    }
}
