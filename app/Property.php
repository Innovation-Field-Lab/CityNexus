<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    protected $fillable = ['full_address', 'house_number', 'street', 'unit', 'city', 'state', 'zip', 'lat', 'long',
        'map', 'lot', 'type'];

    protected $table = 'citynexus_properties';

    public function address()
    {
        return trim($this->house_number . ' ' . $this->street_name . ' ' . $this->unit);
    }
}
