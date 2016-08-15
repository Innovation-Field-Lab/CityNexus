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
    private $wo_pid;
    /**
     * Create a new job instance.
     *
     * @param string $data
     * @param Property $upload_id
     */
    public function __construct($id, $table, $wo_pid = false)
    {
        $this->id = $id;
        $this->table = $table;
        $this->wo_pid = $wo_pid;
    }
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
//      DB::reconnect();

        $tabler = new TableBuilder();
        //Process each individual record
        if(!is_array($this->id))
        {
            $ids[] = $this->id;

        }
        else
        {
            $ids = $this->id;
        }
        foreach($ids as $id)
        {

            try{
                if($this->wo_pid == true)
                {
                    $tabler->processRecordWithoutId($id, $this->table);
                }
                else
                {
                    $tabler->processRecord($id, $this->table);
                }
            }
            catch(\PDOException $e)
            {
                Error::create(['location' => 'processData PDO', 'data' => json_encode(['property_id' => $this->id, 'table' => $this->table])]);
            }
            catch(\Exception $e)
            {
                Error::create(['location' => 'processData', 'data' => json_encode(['property_id' => $this->id, 'table' => $this->table])]);
            }
        }
    }
}