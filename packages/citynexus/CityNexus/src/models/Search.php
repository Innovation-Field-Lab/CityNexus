<?php

namespace CityNexus\CityNexus;

use Illuminate\Database\Eloquent\Model;

class Search extends Model
{
    protected $table = 'citynexus_searches';
    protected $fillable = ['user_id', 'filters', 'name'];
    protected $casts = [
      'filters' => 'array'
    ];

    public function user()
    {
        return $this->belongsTo('\User');
    }

}
