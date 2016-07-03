<?php

namespace CityNexus\CityNexus;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Image extends Model
{
    use SoftDeletes;
    protected $table = 'citynexus_images';
    protected $fillable = ['caption', 'description', 'source', 'location_id'];

    public function property()
    {
        return $this->belongsTo('\CityNexus\CityNexus\Property');
    }

    public function location()
    {
        return $this->belongsTo('\CityNexus\CityNexus\Location');
    }
}
