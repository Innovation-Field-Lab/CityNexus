<?php

namespace CityNexus\CityNexus;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;

class UploadData extends Job implements SelfHandling, ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    private $data;
    private $table;
    private $scheme;
    private $syncValues;
    private $pushValues;
    private $table_name;

    /**
     * Create a new job instance.
     *
     * @param string $elements
     * @param string $table
     * @param Property $property
     */
    public function __construct($data, $table, $scheme, $syncValues, $pushValues)
    {

        $this->data = $data;
        $this->table = $table;
        $this->table_name = $table->table_name;
        $this->scheme = $scheme;
        $this->syncValues = $syncValues;
        $this->pushValues = $pushValues;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $tabler = new TableBuilder();
        //Process each individual record
        foreach($this->data as $i)
        {
            //Create a empty array of the record
            $record = [];

            //if there is a sync value, identify the index id
            if( count( $this->syncValues ) > 0)
            {
                $record[config('tabler.index_id')] = $tabler->findSyncId( config('tabler.index_table'), $i, $this->syncValues);
            }

            //add remaining elements to the array
            $record = $tabler->addElements( $record, $i, $this->scheme);

            foreach($this->scheme as $field)
            {
                if($field->type == 'integer' or $field->type == 'float')
                {
                    if(array_key_exists($field->key, $record)) $record[$field->key] = floatval($record[$field->key]);
                }
            }

            DB::table($this->table_name)->insert($record);

            //If there are push values, update the primary property record
            if(count($this->pushValues) > 0)
            {
                $property = Property::find($record['property_id']);
                foreach ($this->pushValues as $key => $value)
                {
                    $property->$value = $i[$key];
                }
                $property->save();
            }

        }

    }
}
