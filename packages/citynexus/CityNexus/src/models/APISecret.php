<?php

namespace CityNexus\CityNexus;

use Illuminate\Database\Eloquent\Model;

class APISecret extends Model
{
    protected $table = 'citynexus_api_secrets';
    protected $fillable = ['secret', 'user_id'];

}
