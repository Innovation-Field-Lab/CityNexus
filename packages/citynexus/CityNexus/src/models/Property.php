<?php

namespace CityNexus\CityNexus;

use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    protected $fillable = ['full_address', 'house_number', 'street', 'unit', 'city', 'state', 'zip', 'lat', 'long',
        'map', 'lot', 'type', 'tiger_line_id', 'side'];

    protected $table = 'citynexus_properties';

    public function address()
    {
        return trim(trim($this->house_number . ' ' . $this->street_name . ' ' . $this->street_type) . ' ' . $this->unit);
    }
}
