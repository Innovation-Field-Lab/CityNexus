<?php

namespace CityNexus\CityNexus\Http;

use App\Http\Controllers\Controller;
use App\User;
use Carbon\Carbon;
use CityNexus\CityNexus\Note;
use CityNexus\CityNexus\Property;
use CityNexus\CityNexus\DatasetQuery;
use CityNexus\CityNexus\GenerateScore;
use CityNexus\CityNexus\SendEmail;
use CityNexus\CityNexus\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use CityNexus\CityNexus\Table;
use CityNexus\CityNexus\Geocode;
use Illuminate\Support\Facades\Session;


class CitynexusController extends Controller
{

    public function getIndex()
    {
        $widgets = Auth::getUser()->widgets;
        return view('citynexus::dashboards.citymanager', compact('notes', 'widgets'));
    }

    public function getEmailTest()
    {
        return view('citynexus::email.email-template');
    }

    public function getSubmitTicket(Request $request)
    {
        $referer = $request->server->get('HTTP_REFERER');
        return view('citynexus::help.new_ticket', compact('referer'));
    }

    public function postSubmitTicket(Request $request)
    {
        $user = Auth::getUser();

        Mail::send('citynexus::email.submit_ticket', ['request' => $request, 'user' => $user], function ($m) use ($request, $user) {
            $m->from($user->email, $user->fullname());
            $m->to("citynexusorgsupport@citynexussupport.freshdesk.com", "Help Desk")->subject($request->get('subject'));
        });

        Session::flash('flash_success', 'Ticket successfully submitted.');

        return redirect('/');
    }

    /**
     * @param Request $request
     *                  - to: email address
     *                  - subject: email address
     *                  - message: email message, HTML formatted permitted
     *                  - redirect: path to redirect after function called (optional)
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function postSendEmail(Request $request)
    {

        $to = $request->get('to');
        $subject = $request->get('subject');
        $message = $request->get('message');

        $this->dispatch(new SendEmail($to, $subject, $message));

        if($request->get('redirect') != null)
        {
            return redirect($request->get('redirect'));
        }
        else
        {
            return redirect()->back();
        }
    }

    public function getHelp()
    {
        return view('citynexus::help.portal');
    }


}