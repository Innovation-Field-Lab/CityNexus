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


class CreateUnique extends Job implements SelfHandling, ShouldQueue
{
    use InteractsWithQueue, SerializesModels;
    private $table;
    private $column;
    private $values;

    /**
     * Create a new job instance.
     *
     * @param string $data
     * @param Property $upload_id
     */
    public function __construct($table, $column, $values)
    {
        $this->values = $values;
        $this->table = $table;
        $this->column = $column;
    }
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $uid = $this->column;
        foreach($this->values as $value)
        {
            if($value->$uid != null)
            {
                $values = DB::table($this->table)->where($this->column, $value->$uid)->orderBy('created_at', 'DESC')->get();
                $first = array_shift($values);
                foreach($values as $i)
                {
                    DB::table($this->table)->where('id', $i->id)->delete();
                }
            }

        }
    }
}