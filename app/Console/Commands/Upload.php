<?php

namespace App\Console\Commands;

use CityNexus\CityNexus\Dropbox;
use CityNexus\CityNexus\Uploader;
use Illuminate\Console\Command;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Artisan;

class Upload extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'citynexus:dropbox {frequency}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Upload using dropbox API';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Dropbox $dropbox)
    {
        parent::__construct();

        $this->dropbox = $dropbox;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $uploaders = Uploader::where('frequency', $this->argument('frequency'))->get();
        $count = null;
            foreach($uploaders as $i)
            {
                $uploader = Uploader::find($i->id);
                $this->dropbox->processUpload($uploader->settings, $uploader->dataset_id);
                $count++;
            }

        print 'uploaded ' . $this->argument('frequency') . ' ' . $count . 'times';
    }

}
