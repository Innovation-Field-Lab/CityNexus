<?php
namespace CityNexus\CityNexus;
use App\Jobs\Job;
use CityNexus\CityNexus\Http\TablerController;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Toin0u\Geocoder\Facade\Geocoder;


class CleanApartments extends Job implements SelfHandling, ShouldQueue
{
    use InteractsWithQueue, SerializesModels;
    private $addresses;
    private $unit_types;

    /**
     * Create a new job instance.
     *
     * @param string $data
     * @param Property $upload_id
     */
    public function __construct($addresses)
    {
        $this->addresses = $addresses;
        $this->unit_types = config('citynexus.unit_types');

    }
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        foreach($this->addresses as $address)
        {
            if($address->house_number != null && $address->street_name != null)
            {
                $units = Property::where('house_number', $address->house_number)
                    ->where('street_name', $address->street_name)
                    ->where('street_type', $address->street_type)
                    ->get();

                $test_array = $this->createTestArray($units);

                $this->mergeDuplicates($test_array);
            }

        }
    }

    private function createTestArray($units)
    {
        $return = [];
        foreach ($units as $unit)
        {
            $new_unit = str_replace('unit', '', strtolower(str_replace('#', '', $unit->unit)));

            $bits = explode(' ', $new_unit);

            if(!count($bits) > 1)
            {
                $return[$unit->unit][] = $unit->id;
            }
            else{

                // remove bits that are unit names
                foreach($bits as $key => $bit)
                {
                    if(isset($this->unit_types[$bit]))
                    {
                        unset($bits[$key]);
                    }
                }

                // Concact remaining bits
                $clean_unit = '';
                foreach($bits as $bit)
                {
                    $clean_unit = $bit . ' ' . $clean_unit;
                }
                $return[$clean_unit][] = $unit->id;
            }
        }

        return $return;
    }

    private function mergeDuplicates($units)
    {
        foreach ($units as $unit)
        {
            if(count($unit) > 1)
            {
                $this->mergeIds($unit);
            }
        }
    }

    private function mergeIds($ids)
    {
        $first = array_shift($ids);

        $tabler = new TablerController();

        $tabler->mergeProperties($first, $ids);
    }

}