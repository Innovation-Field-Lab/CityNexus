<?php namespace CityNexus\CityNexus;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Table extends Model {

    use SoftDeletes;
    protected $table =  'tabler_tables';
    protected $fillable = ['table_name', 'table_title', 'table_description', 'scheme', 'raw_upload', 'settings'];

    public function uploads()
    {
        return $this->hasMany('CityNexus\CityNexus\Upload');
    }

    public function getSettingAttribute()
    {
        return json_decode($this->settings);
    }

    public function getSchemaAttribute()
    {
        return json_decode($this->scheme);
    }

}