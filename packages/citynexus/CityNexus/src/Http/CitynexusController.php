<?php

namespace CityNexus\CityNexus\Http;

use App\Http\Controllers\Controller;
use App\User;
use CityNexus\CityNexus\GeocodeJob;
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
        return view('citynexus::index');
    }

    public function getProperty($id)
    {

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
        $properties = Property::where('alias_of', null)->get();
        return view('citynexus::property.index', compact('properties'));
    }

    public function getRiskscoreCreate()
    {
        $datasets = Table::all();
        return view('citynexus::risk-score.new', compact('datasets'));
    }

    public function getScores()
    {
        return view('citynexus::risk-score.index')
            ->with('scores', Score::all());
    }

    public function getRunGeocoding()
    {
        $properties = Property::where('lat', null)->orWhere('long', null)->get();

        foreach($properties as $i)
        {
            $id = $i->id;

            $this->dispatch(new GeocodeJob($id));
        }

        return $properties->count();
    }

    public function postSubmitTicket(Request $request)
    {
        Mail::send('citynexus::email.submit_ticket', ['request' => $request], function ($m) use ($request) {
            $m->from('postmaster@citynexus.org', 'CityNexus');
            $m->to("salaback@g.harvard.edu", "Sean Alaback")->subject('New CityNexus Ticket');
            $m->cc($request->get('user_email'));
        });
    }

    private function runScore($score, $elements)
    {
        $properties = Property::all()->chunk(1000);

        $table = 'citynexus_scores_' . $score->id;

        if( !Schema::hasTable($table) )
        {
            $table = Schema::create($table, function (Blueprint $table) {
                $table->increments('id');
                $table->integer('property_id');
                $table->float('score')->nullable();
                $table->timestamps();
            });
        }


//        if(DB::table($table)->count() != 0)  { DB::table($table)->delete(); }

        $jobs = array();



        foreach($properties as $property)
        {
            $this->dispatch(new GenerateScore($elements, $table, $property));
        }
            $this->dispatch(new GenerateScore($elements, $score->id, FALSE));
    }

    public function postAssociateTag(Request $request)
    {
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
        return Property::find($request->get('property_id'))->tags()->detach($request->get('tag_id'));
    }



}