<?php


namespace CityNexus\CityNexus;


use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class DatasetQuery
{
    static function relatedSets( $pid )
    {
        $return = [];

        //Get list of tables
        $tables = Table::all();

        //Query each table for PID
        foreach($tables as $table)
        {
            // Find all records on the table for either the property or an alias
            if($table->table_name != null) {
                $results = DB::table($table->table_name)
                    ->join('citynexus_properties', $table->table_name . '.property_id', '=', 'citynexus_properties.id')
                    ->where('property_id', $pid)
                    ->orWhere('citynexus_properties.alias_of', $pid)
                    ->get([$table->table_name . '.id']);

                //add each object to array
                foreach ($results as $result) {
                    $return[$table->id][] = $results = DB::table($table->table_name)->where('id', $result->id)->first();
                }
            }
        }
        //Return array of related sets
        return $return;
    }   
}