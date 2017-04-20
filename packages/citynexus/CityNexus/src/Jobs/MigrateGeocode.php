<?php
namespace CityNexus\CityNexus;
use App\Jobs\Job;
use App\User;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;

class MigrateGeocode extends Job implements SelfHandling, ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    private $prop_ids;

    /**
     * Create a new job instance.
     *
     * @param string $elements
     * @param string $table
     * @param Property $property
     */
    public function __construct($prop_ids)
    {
        $this->prop_ids = $prop_ids;
    }
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $properties = Property::findMany($this->prop_ids);

        foreach($properties as $property)
        {
            if($property->lat == null && $property->location_id != null)
            {
                $property->lat = $property->location->lat;
                $property->long = $property->location->long;
                $property->save();
            }
        }
    }
}