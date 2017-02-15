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


class BackUpTable extends Job implements SelfHandling, ShouldQueue
{
    use InteractsWithQueue, SerializesModels, DispatchesJobs;
    private $table_name;
    private $email;
    private $user_id;

    /**
     * Create a new job instance.
     *
     * @param string $data
     * @param string $table_id
     * @param Property $upload_id
     */
    public function __construct($table_name, $email, $user_id)
    {
        $this->table_name = $table_name;
        $this->email = $email;
        $this->user_id = $user_id;

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

        $request = new APIRequest();
        $request->request = str_random(24);
        $request->user_id = $this->user_id;
        $request->settings = [
            'type' => 'download',
            'source' => $path
        ];

        $request->save();

        $content = '<p>The file you have requested is now available:<br> <a href="' . action('\CityNexus\CityNexus\Http\APIController@getRequest', [$request->request]) . '">' . action('\CityNexus\CityNexus\Http\APIController@getRequest', [$request->request]) . '</a>.</p><p>This message will self destruct in 24 hours.</p>';

        $this->dispatch(new SendEmail($this->email, 'Download you requested: ' . $this->table_name, $content));
    }
}