<?php
namespace CityNexus\CityNexus;
use App\Jobs\Job;
use App\User;
use CityNexus\CityNexus\Http\TablerController;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;

class MergeProperties extends Job implements SelfHandling, ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    private $prop_ids;
    private $id;

    /**
     * Create a new job instance.
     *
     * @param string $elements
     * @param string $table
     * @param Property $property
     */
    public function __construct($id, $prop_ids)
    {
        $this->prop_ids = $prop_ids;
        $this->id = $id;
    }
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $tabler = new TablerController();

        $tabler->mergeProperties($this->id, $this->prop_ids);
    }
}