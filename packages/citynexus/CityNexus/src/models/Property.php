<?php

namespace CityNexus\CityNexus;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Property extends Model
{
    use SoftDeletes;

    protected $fillable = ['full_address', 'house_number', 'alias_of', 'street_name', 'street_type', 'unit', 'city', 'state', 'zip', 'lat', 'long',
        'map', 'lot', 'type', 'tiger_line_id', 'side', 'location_id'];

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
        return $this->belongsToMany('\CityNexus\CityNexus\Tag', 'property_tag')->whereNull('property_tag.deleted_at')->withTimestamps();
    }

    public function allTags()
    {
        return $this->tags()->lists('tag');
    }

    public function location()
    {
        return $this->belongsTo('\CityNexus\CityNexus\Location');
    }

    public function tasks()
    {
        return $this->morphToMany('\CityNexus\CityNexus\Task', 'citynexus_taskable');
    }

    public function images()
    {
        return $this->hasMany('\CityNexus\CityNexus\Image')->orderBy('created_at', 'DESC');
    }

//    public function getFullAddressAttribute()
//    {
//        return trim($this->house_number . ' ' . $this->street_name . ' ' . $this->street_type . ' ' . $this->unit);
//    }
}
