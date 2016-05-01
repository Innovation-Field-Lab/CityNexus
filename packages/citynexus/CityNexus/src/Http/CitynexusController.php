<?php

namespace CityNexus\CityNexus\Http;

use App\Http\Controllers\Controller;
use App\User;
use Carbon\Carbon;
use CityNexus\CityNexus\GeocodeJob;
use CityNexus\CityNexus\Note;
use CityNexus\CityNexus\Property;
use CityNexus\CityNexus\DatasetQuery;
use CityNexus\CityNexus\GenerateScore;
use CityNexus\CityNexus\Score;
use CityNexus\CityNexus\Setting;
use CityNexus\CityNexus\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use CityNexus\CityNexus\Table;
use CityNexus\CityNexus\ScoreBuilder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Queue;
use CityNexus\CityNexus\Geocode;


class CitynexusController extends Controller
{

    public function getIndex()
    {
        $today = new Carbon();
        $notes = Note::orderBy('created_at')->take(20)->get();
        $pcount = Property::whereNull('alias_of')->count();
        $npcount = Property::whereNull('alias_of')->where('created_at', '<', $today->subMonth(1))->count();
        $tcount = Table::all()->count();

        return view('citynexus::dashboards.citymanager', compact('notes', 'pcount', 'tcount', 'npcount'));
    }

    public function getProperty($id)
    {

        $this->authorize('properties', 'show');

        $property = Property::find($id);
        $datasets = DatasetQuery::relatedSets( $id );
        $tables = Table::all();
        $tags = Tag::select('tag')->lists('tag');
        $apts = Property::where('house_number', $property->house_number)
            ->where('street_name', $property->street_name)
            ->where('street_type', $property->street_type)
            ->where('id', '!=', $property->id)
            ->get();

        // Initiallizes the variable to disclose aliases in dataset
        $disclaimer = false;

        return view('citynexus::property.show', compact('property', 'datasets', 'tables', 'disclaimer', 'tags', 'apts'));
    }

    public function getProperties()
    {
        $this->authorize('properties', 'view');

        $properties = Property::where('alias_of', null)->get();
        return view('citynexus::property.index', compact('properties'));
    }

    public function postSubmitTicket(Request $request)
    {
        Mail::send('citynexus::email.submit_ticket', ['request' => $request], function ($m) use ($request) {
            $m->from('postmaster@citynexus.org', 'CityNexus');
            $m->to("salaback@g.harvard.edu", "Sean Alaback")->subject('New CityNexus Ticket');
            $m->cc($request->get('user_email'));
        });
    }

    public function postAssociateTag(Request $request)
    {
        $this->authorize('properties', 'show');
        //Format Tag
        $tag = ucwords(strtolower($request->get('tag')));

        //Get Tag ID
        $tag = Tag::firstOrCreate(['tag' => $tag]);

        //Associate with property
        Property::find($request->get('property_id'))->tags()->attach($tag);

        //Return snipit
        return view('citynexus::property._tag')->with('tag', $tag);
    }

    public function postRemoveTag(Request $request)
    {
        $this->authorize('properties', 'show');

        return Property::find($request->get('property_id'))->tags()->detach($request->get('tag_id'));
    }



}