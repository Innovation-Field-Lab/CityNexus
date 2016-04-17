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

class InviteUser extends Job implements SelfHandling, ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    private $user_id;

    /**
     * Create a new job instance.
     *
     * @param string $elements
     * @param string $table
     * @param Property $property
     */
    public function __construct($user_id)
    {
        $this->user_id = $user_id;
    }
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        DB::reconnect();

        $user = User::find($this->user_id);
        $token = str_random(36);
        $user->activation = $token;
        $user->save();

        Mail::send('citynexus::email.activate', ['user' => $user, 'token' => $token], function ($m) use ($user) {
            $m->from('postmaster@citynexus.org', 'CityNexus');
            $m->to($user->email, $user->name)->subject('Welcome to CityNexus!');
        });

    }
}