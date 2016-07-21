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


class ProcessData extends Job implements SelfHandling, ShouldQueue
{
    use InteractsWithQueue, SerializesModels;
    private $id;
    private $table;
    /**
     * Create a new job instance.
     *
     * @param string $data
     * @param Property $upload_id
     */
    public function __construct($id, $table)
    {
        $this->id = $id;
        $this->table = $table;
    }
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
//        DB::reconnect();

        $tabler = new TableBuilder();
        //Process each individual record

        try{
            $tabler->processRecord($this->id, $this->table);
        }
        catch(\PDOException $e)
        {
            app('Illuminate\Contracts\Bus\Dispatcher')->dispatch(new ProcessData($this->id, $this->table))->delay(600);
        }
        catch(\Exception $e)
        {
            Error::create(['location' => 'processData', 'data' => ['property_id' => $this->id, 'table' => $this->table]]);
        }

    }
}