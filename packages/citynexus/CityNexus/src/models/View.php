<?php

namespace CityNexus\CityNexus;

use Illuminate\Database\Eloquent\Model;

class View extends Model
{
    protected $table = 'citynexus_report_views';
    protected $fillable = ['name', 'settings', 'access'];

    public function getSettingAttribute()
    {
        return json_decode($this->settings);
    }
}
