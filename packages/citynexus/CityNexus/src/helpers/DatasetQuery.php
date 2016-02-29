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
            if($table->table_name != null) {
                $results = DB::table($table->table_name)->where('property_id', $pid)->get();

                //add each object to array
                foreach ($results as $result) {
                    $return[$table->id][] = $result;
                }
            }
        }

        //Return array of related sets
        return $return;
    }   
}