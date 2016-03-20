<?php

namespace CityNexus\CityNexus\Http;

use App\Http\Controllers\Controller;
use CityNexus\CityNexus\Note;
use CityNexus\CityNexus\Property;
use CityNexus\CityNexus\DatasetQuery;
use CityNexus\CityNexus\GenerateScore;
use CityNexus\CityNexus\Score;
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

        $note = new Note();

        $note->note = $request->get('note');
        $note->property_id = $request->get('property_id');
        $note->user_id = Auth::getUser()->id;
        $note->save();

        return view('citynexus::property._note', compact('note'));

    }

    public function getDelete($id)
    {
        $note = Note::find($id);

        if($note->creator->id == Auth::getUser()->id)
        {
            $note->delete();

            return response("Success", 200);
        }

        return response("Not Authorized", 401);
    }
}