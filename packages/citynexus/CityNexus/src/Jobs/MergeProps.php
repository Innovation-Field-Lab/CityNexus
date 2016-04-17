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

        foreach($this->prop_ids as $i)
        {
            if($i != null) {

                $property = Property::find($i);

                $alias_of = Property::where('full_address', $property->full_address)
                    ->whereNotNull('alias_of')
                    ->pluck('alias_of');

                if ($alias_of != null) {
                    $i = $alias_of;
                }

                Property::where('id', '!=', $property->id)
                    ->where('full_address', $property->full_address)
                    ->update(['alias_of' => $i]);
            }
        }
    }
}