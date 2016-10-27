<?php

namespace CityNexus\CityNexus\Http;

use App\Http\Controllers\Controller;
use App\User;
use CityNexus\CityNexus\Property;
use CityNexus\CityNexus\DatasetQuery;
use CityNexus\CityNexus\GenerateScore;
use CityNexus\CityNexus\Score;
use CityNexus\CityNexus\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use CityNexus\CityNexus\Table;
use CityNexus\CityNexus\ScoreBuilder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Session;
use League\Flysystem\Exception;
use CityNexus\CityNexus\InviteUser;
use Illuminate\Support\Facades\Hash;


class HelpController extends Controller
{
    public function getItem( $file )
    {
        return view('citynexus::help.' . $file);
    }

    public function getSubmitTicket(Request $request)
    {
        $referer = $request->server->get('HTTP_REFERER');
        return view('citynexus::help.new_ticket', compact('referer'));
    }

    public function postSubmitTicket(Request $request)
    {
        $user = Auth::user();

        Mail::send('citynexus::email.submit_ticket', ['request' => $request, 'user' => $user], function ($m) use ($request, $user) {
            $m->from($user->email, $user->fullname());
            $m->to("support@citynexus.zendesk.com", "Help Desk")->subject($request->get('subject'));
        });

        Session::flash('flash_success', 'Ticket successfully submitted.');

        return redirect('/');
    }
}