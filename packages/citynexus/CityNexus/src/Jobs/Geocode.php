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

class GeocodeJob extends Job implements SelfHandling, ShouldQueue
{
    use InteractsWithQueue, SerializesModels;
    private $p_id;
    /**
     * Create a new job instance.
     *
     * @param string $elements
     * @param string $table
     * @param Property $property
     */
    public function __construct($id)
    {
        $this->p_id = $id;
    }
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        DB::reconnect();

        try
        {
            // Get property record
            $property = Property::find($this->p_id);
            $geocode = Geocoder::geocode(   $property->full_address  . ', ' . config('citynexus.city_state'));

            if($geocode)
            {
                $location = Location::create([
                    'lat' => $geocode->getLatitude(),
                    'long' => $geocode->getLongitude(),
                    'full_address' => $property->full_address,
                ]);
            }

            $property->save();
        }
        catch(\Exception $e)
        {
            Error::create(['location' => 'GeoCode Job', 'data' => json_encode(['pid' => $this->p_id, 'e' => $e])]);
        }

    }
}