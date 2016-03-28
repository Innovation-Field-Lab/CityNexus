<?php

namespace CityNexus\CityNexus;

use Illuminate\Database\Eloquent\Model;

class Upload extends Model
{
    protected $table = 'citynexus_uploads';
    protected $fillable = ['table_id', 'note'];

    public function table()
    {
        return $this->belongsTo('CityNexus\CityNexus\Table');
    }

}
