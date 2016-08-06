<?php

namespace CityNexus\CityNexus;

use Illuminate\Database\Eloquent\Model;

class Uploader extends Model
{
    protected $table = 'citynexus_uploaders';

    protected $fillable = ['dataset_id', 'name', 'type', 'frequency', 'settings_json'];

    public function dataset()
    {
        return $this->belongsTo('\CityNexus\CityNexus\Table', 'dataset_id');
    }

    public function getSettingsAttribute()
    {
        return json_decode($this->settings_json);
    }
}
