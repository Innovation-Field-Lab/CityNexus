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
        return $this->hasMany('\CityNexus\CityNexus\Note')->orderBy('created_at', 'DESC')->whereNull('reply_to');
    }

    public function tags()
    {
        return $this->belongsToMany('\CityNexus\CityNexus\Tag', 'property_tag')->whereNull('property_tag.deleted_at')->orderBy('property_tag.created_at', 'desc')->withTimestamps()->withPivot('created_by');
    }

    public function trashedTags()
    {
        return $this->belongsToMany('\CityNexus\CityNexus\Tag', 'property_tag')->whereNotNull('property_tag.deleted_at')->orderBy('property_tag.deleted_at', 'desc')->withTimestamps()->withPivot('created_by', 'deleted_by', 'deleted_at');
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

    public function files()
    {
        return $this->hasMany('\CityNexus\CityNexus\File')->orderBy('created_at', 'DESC');
    }

//    public function getFullAddressAttribute()
//    {
//        return trim($this->house_number . ' ' . $this->street_name . ' ' . $this->street_type . ' ' . $this->unit);
//    }
}
