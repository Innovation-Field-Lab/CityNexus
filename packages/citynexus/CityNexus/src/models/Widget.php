<?php

namespace CityNexus\CityNexus;

use Illuminate\Database\Eloquent\Model;

class Widget extends Model
{
    protected $table = 'citynexus_widgets';

    protected $fillable = ['type', 'size', 'settings', 'name', 'description'];

    public function getSettingAttribute()
    {
        return json_decode($this->settings);
    }
}
