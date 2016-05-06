<?php

namespace CityNexus\CityNexus;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $table = 'citynexus_settings';

    protected $id = 'key';

    protected $fillable = ['key', 'value'];

}
