<?php
namespace CityNexus\CityNexus;
use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Toin0u\Geocoder\Facade\Geocoder;


class BackUpTable extends Job implements SelfHandling, ShouldQueue
{
    use InteractsWithQueue, SerializesModels;
    private $table_name;

    /**
     * Create a new job instance.
     *
     * @param string $data
     * @param string $table_id
     * @param Property $upload_id
     */
    public function __construct($table_name)
    {
        $this->table_name = $table_name;
    }
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        if($this->table_name != null)  $table = DB::table($this->table_name)->get();

        $path = storage_path() . '/' . time() . '_' . $this->table_name . '.csv';

        $fp = fopen($path, 'w+');

        if(count($table) > 0)
        {
            foreach(current($table) as $key => $value)
            {
                $keys[] = $key;
            }

            if(isset($keys)) fputcsv($fp, (array) $keys);
        }

        foreach($table as $row)
        {
            fputcsv($fp, (array) $row);
        }

        fclose($fp);

        $s3 = Storage::disk('s3');
        $filePath = '/db_table_backups/' . date('dmY') . '/' . time() . '_' . $this->table_name . '.csv' ;
        $s3->put($filePath, file_get_contents($path), 'public');

    }
}