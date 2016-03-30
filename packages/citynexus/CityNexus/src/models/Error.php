<?php

namespace CityNexus\CityNexus;

use Illuminate\Database\Eloquent\Model;

class Error extends Model
{
    protected $fillable = ['location', 'data'];
}
