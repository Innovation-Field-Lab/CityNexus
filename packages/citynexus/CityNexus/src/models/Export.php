<?php

namespace CityNexus\CityNexus;

use Illuminate\Database\Eloquent\Model;

class Export extends Model
{
    protected $table = 'citynexus_exports';
    protected $fillable = ['name', 'elements', 'source'];
    protected $casts = [
        'elements' => 'array'
    ];
}
