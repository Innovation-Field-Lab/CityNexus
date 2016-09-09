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

class MergeProps extends Job implements SelfHandling, ShouldQueue
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
        DB::reconnect();

        if(!is_array($this->prop_ids))
        {
            $ids[] = $this->prop_ids;
        }
        else
        {
            $ids = $this->prop_ids;
        }

        foreach($ids as $i)
        {
            $property = Property::find($i);

            if($property == null)
            {
                break;
            }

            $properties = Property::where('id', '!=', $property->id)
                ->where('alias_of', $property->id)
                ->lists('id');

            $datasets = DB::table('tabler_tables')->whereNotNull('table_name')->lists('table_name');
            if(!is_array($properties) && is_integer($properties))
            {
                $properties[] = $properties;
            }
            if($properties != null) {
                foreach ($properties as $prop) {
                    DB::table('citynexus_images')->where('property_id', $prop)->update(['property_id' => $i]);
                    DB::table('citynexus_notes')->where('property_id', $prop)->update(['property_id' => $i]);
                    DB::table('citynexus_properties')->where('alias_of', $prop)->update(['alias_of' => $i]);
                    DB::table('citynexus_raw_addresses')->where('property_id', $prop)->update(['property_id' => $i]);
                    DB::table('citynexus_taskables')->where('citynexus_taskable_id', $prop)->where('citynexus_taskable_type', 'CityNexus\CityNexus\Property')->update(['citynexus_taskable_id' => $i]);
                    DB::table('property_tag')->where('property_id', $prop)->update(['property_id' => $i]);

                    foreach ($datasets as $tn) {
                        if ($tn != 'assessors_dept' && $tn != 'tabler_police_incident_report') {
                            DB::table($tn)->where('property_id', $prop)->update(['property_id' => $i]);
                        }
                    }

                    DB::table('citynexus_properties')->where('id', $prop)->delete();
                }
            }
        }
    }
}