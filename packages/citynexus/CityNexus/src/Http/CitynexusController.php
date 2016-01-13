<?php

namespace CityNexus\CityNexus\Http;

use App\Http\Controllers\Controller;
use App\Property;
use CityNexus\CityNexus\DatasetQuery;
use Illuminate\Http\Request;
use Salaback\Tabler\Table;
use Yajra\Datatables\Datatables;


class CitynexusController extends Controller
{

    public function getProperty(Request $request)
    {
        $property = Property::find($request->get('property_id'));
        $datasets = DatasetQuery::relatedSets( $property->id );
        return view('citynexus::property.show', compact('property', 'datasets'));
    }

    public function getProperties()
    {
        $properties = Property::all();
        return view('citynexus::index', compact('properties'));
    }

    public function getPropertiesData()
    {
        return Datatables::of(Property::select('*'))->make(true);
    }

    public function getRiskscoreCreate()
    {
        $datasets = Table::all();
        return view('citynexus::risk-score.new', compact('datasets'));
    }

    public function getRiskscoreDatafields(Request $request)
    {
        $dataset = Table::find($request->get('dataset_id'));
        $scheme = json_decode($dataset->scheme);

        return view('citynexus::risk-score.datafields', compact('dataset', 'scheme'));
    }

    public function getRiskscoreDatafield(Request $request)
    {
        $dataset = Table::find($request->get('table_id'));
        $scheme = json_decode($dataset->scheme, false);
        $key = $request->get('key');
        $field = $scheme->$key;

        return view('citynexus::risk-score.fieldsettings', compact('dataset', 'scheme', 'field'));
    }

    public function postCreateElement(Request $request)
    {
        return $request->get('form_data');
    }
}