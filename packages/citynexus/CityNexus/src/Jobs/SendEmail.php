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

class SendEmail extends Job implements SelfHandling, ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    private $to;
    private $subject;
    private $message;

    /**
     * Create a new job instance.
     *
     * @param $to string
     * @param $subject string
     * @param $message string

     */
    public function __construct($to, $subject, $message)
    {
        $this->to = $to;
        $this->subject = $subject;
        $this->message = $message;
    }
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        DB::reconnect();

        $to = $this->to;
        $subject = $this->subject;
        $content = $this->message;

        Mail::send('citynexus::email.basic_email', ['content' => $content], function ($m) use ($to, $subject) {
            $m->from('postmaster@citynexus.org', 'CityNexus');
            $m->to($to)->subject($subject);
        });

    }
}