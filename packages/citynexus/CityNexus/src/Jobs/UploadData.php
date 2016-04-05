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
     * @param string $data
     * @param string $table_id
     * @param Property $upload_id
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

            try
            {
                $id = $tabler->addRecord($this->data, $this->tableId, $this->uploadId);

            }
            catch(\Exception $e)
            {
                Error::create(['location' => 'UploadData', 'data' => json_encode(['data' => $this->data, 'table_id' => $this->tableId, 'upload_id' => $this->uploadId, 'error' => $e, ])]);
            }

            try
            {
                // Get property record
                $property = Property::find($id);
                if($property->lat == null | $property->long == null) {
                }
                $geocode = Geocoder::geocode(   $property->full_address  . ', ' . config('citynexus.city_state'));

                if($geocode)
                {
                    $property->lat = $geocode->getLatitude();
                    $property->long = $geocode->getLongitude();
                }

                $property->save();
            }
            catch(\Exception $e)
            {
                $data['e'] = $e;
                if(isset($id)) $data['id'] = $id; else $data['id'] = null;
                $data['data'] = $this->data;
                $data['table_id'] = $this->tableId;
                $data['uploadId'] = $this->uploadId;

                Error::create(['location' => 'GeoCode on UploadData', 'data' => json_encode($data)]);
            }

    }
}