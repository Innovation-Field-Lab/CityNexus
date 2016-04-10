<?php

namespace CityNexus\CityNexus;

use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    protected $fillable = ['full_address', 'house_number', 'street_name', 'street_type', 'unit', 'city', 'state', 'zip', 'lat', 'long',
        'map', 'lot', 'type', 'tiger_line_id', 'side'];

    protected $table = 'citynexus_properties';

    public function address()
    {
        return trim(trim($this->house_number . ' ' . $this->street_name . ' ' . $this->street_type) . ' ' . $this->unit);
    }

    public function aliases()
    {
        return $this->hasMany('\CityNexus\CityNexus\Property', 'alias_of');
    }

    public function aliasOf()
    {
        return $this->belongsTo('\CityNexus\CityNexus\Property', 'alias_of');
    }

    public function notes()
    {
        return $this->hasMany('\CityNexus\CityNexus\Note');
    }

    public function tags()
    {
        return $this->belongsToMany('\CityNexus\CityNexus\Tag', 'property_tag');
    }

    public function allTags()
    {
        return $this->tags()->lists('tag');
    }
}
