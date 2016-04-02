<?php

namespace CityNexus\CityNexus;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $table = 'citynexus_tags';
    protected $fillable = ["tag"];
    public $timestamps = false;

    public function properties()
    {
        return $this->belongsToMany('\CityNexus\CityNexus\Property', 'property_tag');
    }
}
