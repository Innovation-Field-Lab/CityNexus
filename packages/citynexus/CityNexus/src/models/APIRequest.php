<?php

namespace CityNexus\CityNexus;

use Illuminate\Database\Eloquent\Model;

class APIRequest extends Model
{
    protected $table = 'citynexus_api_requests';
    protected $fillable = ['request_key', 'user_id', 'type', 'settings'];
    protected $casts = [
        'settings' => 'array'
    ];
}
