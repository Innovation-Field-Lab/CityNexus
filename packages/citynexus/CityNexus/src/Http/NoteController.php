<?php

namespace CityNexus\CityNexus\Http;

use App\Http\Controllers\Controller;
use CityNexus\CityNexus\Note;
use CityNexus\CityNexus\Property;
use CityNexus\CityNexus\DatasetQuery;
use CityNexus\CityNexus\GenerateScore;
use CityNexus\CityNexus\Score;
use CityNexus\CityNexus\SendEmail;
use CityNexus\CityNexus\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use CityNexus\CityNexus\Table;
use CityNexus\CityNexus\ScoreBuilder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;


class NoteController extends Controller
{
    public function postStore(Request $request)
    {
        $this->validate($request, [
                'note' => 'required',
                'property_id' => 'required'
            ]);

        if($request->get('reply_to') == null)
        {
            $note = $this->saveReplyNote($request);
        } else {
            $note = $this->saveNewNote($request);
        }

        return view('citynexus::property._note', compact('note'));

    }


    public function getDelete($id)
    {
        $note = Note::find($id);

        if($note->creator->id == Auth::getUser()->id | Auth::getUser()->super_admin == true)
        {
            $note->delete();

            return response("Success", 200);
        }

        return response("Not Authorized", 401);
    }

    private function saveNewNote($request)
    {
        $note = new Note();
        $note->note = $request->get('note');
        $note->property_id = $request->get('property_id');
        $note->user_id = Auth::getUser()->id;
        $note->save();

        return $note;
    }

    private function saveReplyNote($request)
    {
        $note = new Note();
        $note->note = $request->get('note');
        $note->reply_to = $request->get('reply_to');
        $note->property_id = $request->get('property_id');
        $note->user_id = Auth::getUser()->id;
        $note->save();

        $creator = Note::find($request->get('reply_to'))->creator;
        $property = Property::find($note->property_id);

        $message = '<p>Hi ' . $creator->first_name . '</p>';
        $message .= '<p>' . Auth::user()->full_name . ' has replied to your comment on <a href="' . action('\CityNexus\CityNexus\Http\PropertyController@getShow', [$property->id]) . '">' . ucwords($property->full_address) . '</a>:</p>';
        $message .= '<p>' . $note->note . '</p>';
        $this->dispatch(new SendEmail($creator->email, 'Reply to your comment', $message));

        return $note;
    }
}