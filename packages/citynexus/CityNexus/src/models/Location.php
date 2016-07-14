<?php

namespace CityNexus\CityNexus;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $table = 'citynexus_locations';
    protected $fillable = ['lat', 'long', 'description', 'full_address', 'source', 'address', 'polygon', 'street_number', 'street_name', 'locality', 'postal_code', 'sub_locality', 'admin_levels', 'country', 'country_code', 'timezone'];
}
