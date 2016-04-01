<?php

namespace CityNexus\CityNexus;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $table = 'citynexus_tags';
    protected $fillable = ["tag"];
}
