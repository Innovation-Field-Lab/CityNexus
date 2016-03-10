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
    private $options;

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
        $this->options = [
            'table' => $table,
            'tableName' => $table->table_name,
            'scheme' => $scheme,
            'syncValues' => $syncValues,
            'pushValues' => $pushValues
        ];

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
            $tabler->addRecord($i, $this->options);
        }

    }
}
