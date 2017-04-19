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
use Illuminate\Support\Facades\Storage;
use Toin0u\Geocoder\Facade\Geocoder;


class CheckForDuplicates extends Job implements SelfHandling, ShouldQueue
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
        $property = Property::find($this->id);
        $test = DB::table('citynexus_properties')->where('full_address', trim($property->full_address))->lists('id');

        if(count($test) > 1)
        {
            $this->dispatch(new MergeProps($test));
        }

    }
}