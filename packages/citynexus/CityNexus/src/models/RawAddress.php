<?php

namespace CityNexus\CityNexus;

use Illuminate\Database\Eloquent\Model;

class RawAddress extends Model
{
    protected $table = 'citynexus_raw_addresses';
    protected $fillable = ['address', 'property_id'];
}
