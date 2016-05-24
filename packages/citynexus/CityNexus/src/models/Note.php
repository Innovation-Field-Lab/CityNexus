<?php namespace CityNexus\CityNexus;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class Note extends Model {

    use SoftDeletes;

    protected $fillable = ['note', 'property_id', 'user_id'];

    protected $table = 'citynexus_notes';

    public function creator()
    {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }

    public function property()
    {
        return $this->belongsTo('\CityNexus\CityNexus\Property');
    }
}
