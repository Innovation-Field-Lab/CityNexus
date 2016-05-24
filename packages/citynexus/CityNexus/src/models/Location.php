<?php

namespace CityNexus\CityNexus;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $table = 'citynexus_locations';
    protected $fillable = ['lat', 'long', 'description', 'full_address', 'source'];
}
