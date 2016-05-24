<?php

namespace CityNexus\CityNexus\Http;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use CityNexus\CityNexus\Note;
use CityNexus\CityNexus\Property;
use CityNexus\CityNexus\DatasetQuery;
use CityNexus\CityNexus\GenerateScore;
use CityNexus\CityNexus\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use CityNexus\CityNexus\Table;
use CityNexus\CityNexus\Geocode;


class CitynexusController extends Controller
{

    public function getIndex()
    {
        $notes = Note::orderBy('created_at', "DEC")->take(20)->get();
        return view('citynexus::dashboards.citymanager', compact('notes'));
    }

//    public function getProperty($id)
//    {
//        $this->authorize('citynexus', [ 'properties',  'show']);
//
//        $property = Property::find($id);
//        $datasets = DatasetQuery::relatedSets( $id );
//        $tables = Table::all();
//        $tags = Tag::select('tag')->lists('tag');
//        $apts = Property::where('house_number', $property->house_number)
//            ->where('street_name', $property->street_name)
//            ->where('street_type', $property->street_type)
//            ->where('id', '!=', $property->id)
//            ->get();
//
//        // Initiallizes the variable to disclose aliases in dataset
//        $disclaimer = false;
//
//        return view('citynexus::property.show', compact('property', 'datasets', 'tables', 'disclaimer', 'tags', 'apts'));
//    }
//
//    public function getProperties()
//    {
//        $this->authorize('citynexus', ['properties', 'view']);
//
//        $properties = Property::where('alias_of', null)->get();
//        return view('citynexus::property.index', compact('properties'));
//    }

    public function postSubmitTicket(Request $request)
    {
        Mail::send('citynexus::email.submit_ticket', ['request' => $request], function ($m) use ($request) {
            $m->from('postmaster@citynexus.org', 'CityNexus');
            $m->to("salaback@g.harvard.edu", "Sean Alaback")->subject('New CityNexus Ticket');
            $m->cc($request->get('user_email'));
        });
    }

//    public function postAssociateTag(Request $request)
//    {
//        $this->authorize('citynexus', [ 'properties', 'show']);
//        //Format Tag
//        $tag = ucwords(strtolower($request->get('tag')));
//
//        //Get Tag ID
//        $tag = Tag::firstOrCreate(['tag' => $tag]);
//
//        //Associate with property
//        Property::find($request->get('property_id'))->tags()->attach($tag);
//
//        //Return snipit
//        return view('citynexus::property._tag')->with('tag', $tag);
//    }
//
//    public function postRemoveTag(Request $request)
//    {
//        $this->authorize('citynexus', ['properties', 'show']);
//
//        return Property::find($request->get('property_id'))->tags()->detach($request->get('tag_id'));
//    }



}