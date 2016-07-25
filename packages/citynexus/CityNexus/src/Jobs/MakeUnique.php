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


class MakeUnique extends Job implements SelfHandling, ShouldQueue
{
    use InteractsWithQueue, SerializesModels;
    private $ids;
    /**
     * Create a new job instance.
     *
     * @param string $data
     * @param Property $upload_id
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
//        DB::reconnect();

       foreach($this->ids as $i)
       {
           $now = db::table('tabler_police_calls')->where('callnum', $i->callnum)->where('id', '!=', $i->id)->delete();
       }


    }
}