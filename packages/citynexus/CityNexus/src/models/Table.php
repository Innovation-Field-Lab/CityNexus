<?php namespace CityNexus\CityNexus;

use Illuminate\Database\Eloquent\Model;

class Table extends Model {

    protected $table =  'tabler_tables';
    protected $fillable = ['table_name', 'table_description', 'scheme', 'raw_upload'];

}