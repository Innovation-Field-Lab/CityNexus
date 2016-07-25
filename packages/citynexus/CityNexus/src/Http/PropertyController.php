<?php

namespace CityNexus\CityNexus\Http;

use App\Http\Controllers\Controller;
use App\User;
use CityNexus\CityNexus\GeocodeJob;
use CityNexus\CityNexus\Property;
use CityNexus\CityNexus\DatasetQuery;
use CityNexus\CityNexus\Score;
use CityNexus\CityNexus\Setting;
use CityNexus\CityNexus\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use CityNexus\CityNexus\Table;
use CityNexus\CityNexus\ScoreBuilder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Session;
use League\Flysystem\Exception;
use CityNexus\CityNexus\InviteUser;
use Illuminate\Support\Facades\Hash;


class PropertyController extends Controller
{
    public function getIndex()
    {
        $this->authorize('citynexus', ['properties', 'view']);

        $properties = Property::whereNull('alias_of')->whereNotNull('house_number')->get(['house_number', 'street_name', 'street_type', 'unit', 'id']);
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
        $users = User::where('last_name', '>=', 'Alaback')->orderBy('last_name')->get();

        return view('citynexus::property.show', compact('users', 'property', 'datasets', 'tables', 'disclaimer', 'tags', 'apts'));
    }

    public function postAssociateTag(Request $request)
    {
        $this->authorize('citynexus', [ 'properties', 'show']);
        //Format Tag
        $tag = ucwords(strtolower($request->get('tag')));

        //Get Tag ID
        $tag = Tag::firstOrCreate(['tag' => $tag]);

        //Associate with property
        Property::find($request->get('property_id'))->tags()->attach($tag, ['created_by' => Auth::getUser()->id]);

        //Return snipit
        return view('citynexus::property._tag')->with('tag', $tag);
    }

    public function postRemoveTag(Request $request)
    {
        $this->authorize('citynexus', ['properties', 'show']);

        DB::table('property_tag')
            ->where('property_id', $request->get('property_id'))
            ->where('tag_id', $request->get('tag_id'))
            ->whereNull('deleted_at')
            ->update(['deleted_at' => DB::raw('NOW()'), 'deleted_by' => Auth::getUser()->id]);
    }

    public function getCreate()
    {
        $this->authorize('citynexus', [ 'properties', 'create']);

        return view('citynexus::property.create');
    }

    public function postCreate(Request $request)
    {
        $this->validate($request, [
            'house_number' => 'integer|required',
            'street_name' => 'max:150|required',
            'street_type' => 'required',
            'unit' => 'max:50',
            'full_address' => 'max:255'
        ]);

        $property = $request->all();
        $property['street_name'] = strtolower($property['street_name']);
        $property['unit'] = strtolower($property['unit']);

        $property = Property::create($property);

        if('local' != env('APP_ENV') or 'testing' != env('APP_ENV'))
        {
            $this->dispatch(new GeocodeJob($property->id));
        }

        return redirect(action('\CityNexus\CityNexus\Http\PropertyController@getShow', ['id' => $property->id]));
    }

    public function getUpdate($id)
    {
        $property = Property::find($id);

        return view('citynexus::property.update', compact('property'));
    }

    public function postUpdate($id, Request $request)
    {
        $this->authorize('citynexus', ['property', 'edit']);
        $this->validate($request, [
            'house_number' => 'max:20|required',
            'street_name' => 'max:250|required',
            'unit' => 'max:100'
        ]);
        $property = Property::find($id);
        $property->update($request->all());

        Session::flash('flash_success', 'Property address updated');
        return redirect()->back();
    }

    public function getDelete($id)
    {
        $this->authorize('citynexus', ['property', 'delete']);
        try
        {
            Property::find($id)->delete();
        }
        catch(\Exception $e)
        {
            Session::flash('flash_error', "Oh oh! Something went wrong.");
            return redirect()->back();
        }
        Session::flash('flash_info', 'Property deleted.');
        return redirect(action('\CityNexus\CityNexus\Http\PropertyController@getIndex'));
    }


}