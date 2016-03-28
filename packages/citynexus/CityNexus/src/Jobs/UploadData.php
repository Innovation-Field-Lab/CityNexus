<?php
namespace CityNexus\CityNexus;
use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Toin0u\Geocoder\Facade\Geocoder;


class UploadData extends Job implements SelfHandling, ShouldQueue
{
    use InteractsWithQueue, SerializesModels;
    private $data;
    private $tableId;
    private $uploadId;
    /**
     * Create a new job instance.
     *
     * @param string $elements
     * @param string $table
     * @param Property $property
     */
    public function __construct($data, $table_id, $upload_id)
    {
        $this->data = $data;
        $this->tableId = $table_id;
        $this->uploadId = $upload_id;
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
            $id = $tabler->addRecord($i, $this->tableId, $this->uploadId);

            // Get property record
            $property = Property::find($id);
            $geocode = Geocoder::geocode(   $property->full_address  . ', ' . config('citynexus.city_state'));

            if($geocode)
            {
                $property->lat = $geocode->getLatitude();
                $property->long = $geocode->getLongitude();
            }

            $property->save();

        }

    }
}