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


class ProcessData extends Job implements SelfHandling, ShouldQueue
{
    use InteractsWithQueue, SerializesModels;
    private $id;
    private $table;
    /**
     * Create a new job instance.
     *
     * @param string $data
     * @param string $table_id
     * @param Property $upload_id
     */
    public function __construct($id, $table)
    {
        $this->id = $id;
        $this->table = $table;
    }
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
//        DB::reconnect();

        $tabler = new TableBuilder();
        //Process each individual record

        $table = Table::find($this->table_id);

        try
        {
            $id = $tabler->processRecord($this->id, $this->table);

        }
        catch(\Exception $e)
        {
            Error::create(['location' => 'Process Uploader', 'data' => json_encode(['id' => $this->id, 'table' => $this->table, 'error' => $e])]);
        }

        try
        {
            // Get property record
            $property = Property::find($id);
            if($property->location_id == null) {
                $location = Location::firstOrCreate(['full_address' => $property->house_number . ' ' . $property->street_name . ' ' . $property->street_type]);
             if($location->lat == null && env('APP_ENV') != 'testing' && env('APP_ENV') != 'local')
             {
                 $geocode = Geocoder::geocode(   $property->full_address  . ', ' . config('citynexus.city_state'));
                 $location->lat = $geocode->getLatitude();
                 $location->long = $geocode->getLongitude();
             }
                $property->location_id = $location->id;
                $property->save();
            }

        }
        catch(\Exception $e)
        {
            $data['e'] = $e;
            if(isset($id)) $data['id'] = $id; else $data['id'] = null;
            $data['id'] = $this->id;
            $data['table'] = $this->table;

            Error::create(['location' => 'GeoCode on UploadData', 'data' => json_encode($data)]);
        }

    }
}