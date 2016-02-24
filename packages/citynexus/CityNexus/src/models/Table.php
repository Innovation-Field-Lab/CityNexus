<?php namespace Salaback\Tabler;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class Table extends Model {

    protected $table =  'tabler_tables';
    protected $fillable = ['table_name', 'table_description', 'scheme', 'raw_upload'];

}