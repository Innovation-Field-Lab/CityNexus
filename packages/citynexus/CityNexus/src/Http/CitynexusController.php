<?php

namespace CityNexus\CityNexus\Http;

use App\Http\Controllers\Controller;
use App\Property;
use CityNexus\CityNexus\DatasetQuery;
use CityNexus\CityNexus\Score;
use Illuminate\Http\Request;
use Salaback\Tabler\Table;
use Yajra\Datatables\Datatables;
use CityNexus\CityNexus\ScoreBuilder;


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
        unset($dataset['_token']);
        return view('citynexus::risk-score.fieldsettings', compact('dataset', 'scheme', 'field'));
    }

    public function getCreateElement(Request $request)
    {
        $element = $request->all();

        return view('citynexus::risk-score.element', compact('element'));
    }

    public function postSaveScore(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:255',
            'elements' => 'required'
        ]);
        // test if score if there is a score id

        // if there is a score id, update the fields

        // if no score id save as new score
            $elements = array();
            foreach($request->get('elements') as $i)
            {
                $elements[] = json_decode($i);
            }

            $score = new Score;
            $score->name = $request->get('name');
            $score->elements = json_encode($elements);
            $score->save();

        return redirect( config('citynexus.root_directory') . '/scores' );
    }

    public function getGenerateScore(Request $request)
    {
        $builder = new ScoreBuilder();
        $score = Score::find($request->get('score_id'));
        $elements = json_decode($score->elements);

        $record = Property::find($request->get('id'));

        $result = $builder->genScore($record, $elements);



        return view('citynexus::risk-score.generate', compact('score', 'record', 'elements', 'result'));
    }
}