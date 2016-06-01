<?php

namespace CityNexus\CityNexus\Http;

use App\Http\Controllers\Controller;
use App\User;
use CityNexus\CityNexus\Property;
use CityNexus\CityNexus\DatasetQuery;
use CityNexus\CityNexus\GenerateScore;
use CityNexus\CityNexus\Tag;
use Illuminate\Http\Request;
use CityNexus\CityNexus\Table;


class PropertyController extends Controller
{
    public function getIndex()
    {
        $this->authorize('citynexus', ['properties', 'view']);

        $properties = Property::where('alias_of', null)->get();
        return view('citynexus::property.index', compact('properties'));
    }

    public function getShow($id)
    {
        $this->authorize('citynexus', [ 'properties',  'show']);

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

    public function postAssociateTag(Request $request)
    {
        $this->authorize('citynexus', [ 'properties', 'show']);
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
        $this->authorize('citynexus', ['properties', 'show']);

        return Property::find($request->get('property_id'))->tags()->detach($request->get('tag_id'));
    }
}