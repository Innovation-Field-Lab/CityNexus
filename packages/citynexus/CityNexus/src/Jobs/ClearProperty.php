<?php
namespace CityNexus\CityNexus;
use App\Jobs\Job;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Toin0u\Geocoder\Facade\Geocoder;


class ClearProperty extends Job implements SelfHandling, ShouldQueue
{
    use InteractsWithQueue, SerializesModels, DispatchesJobs;

    private $id;
    /**
     * Create a new job instance.
     *
     * @param string $data
     * @param string $table_id
     * @param Property $upload_id
     */
    public function __construct($id)
    {
        $this->id;
    }
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $datasets = DB::table('tabler_tables')->whereNotNull('table_name')->lists('table_name');

        $count = 0;
        $count += DB::table('citynexus_images')->where('property_id', $this->id)->count();
        $count += DB::table('citynexus_notes')->where('property_id', $this->id)->count();
        $count += DB::table('citynexus_properties')->where('alias_of', $this->id)->count();
        $count += DB::table('citynexus_taskables')->where('citynexus_taskable_id', $this->id)->where('citynexus_taskable_type', 'CityNexus\CityNexus\Property')->count();
        $count += DB::table('property_tag')->where('property_id', $this->id)->count();
        if($count == 0)
        {
            foreach ($datasets as $tn) {
                if (Schema::hasTable($tn)) {
                    DB::table($tn)->where('property_id', $this->id)->update(['property_id' => null]);
                }
            }
            DB::table('citynexus_raw_addresses')->where('property_id', $this->id)->delete();

            Property::find($this->id)->delete();
        }

    }
}