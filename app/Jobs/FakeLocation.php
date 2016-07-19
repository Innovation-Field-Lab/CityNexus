<?php

namespace App\Jobs;

use App\Jobs\Job;
use CityNexus\CityNexus\Location;
use Illuminate\Contracts\Bus\SelfHandling;
use CityNexus\CityNexus\Property;
class FakeLocation extends Job implements SelfHandling
{

    protected $id;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($ids)
    {
        $this->ids = $ids;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        foreach($this->ids as $i)
        {
            try{
                $property = Property::find($i->id);
                $location = Location::find($property->location_id);
                if($location == null)
                {
                    $location = Location::firstOrCreate(['full_address' => $property->full_address]);
                }

                if($property->lat != null && $location->lat == null)
                {
                    $location->lat = $property->lat;
                    $location->long = $property->long;
                    $location->save();
                }
            }
            catch(\Exception $e)
            {
                dd($e);
            }

        }
    }
}
