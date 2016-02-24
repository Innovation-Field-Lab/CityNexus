<?php

namespace CityNexus\CityNexus\Http;

use App\Http\Controllers\Controller;
use CityNexus\CityNexus\Property;
use Carbon\Carbon;
use CityNexus\CityNexus\DatasetQuery;
use CityNexus\CityNexus\GenerateScore;
use CityNexus\CityNexus\Score;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Salaback\Tabler\Table;
use CityNexus\CityNexus\ScoreBuilder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;


class CitynexusController extends Controller
{

    public function getProperty(Request $request)
    {
        $property = Property::find($request->get('property_id'));
        $datasets = DatasetQuery::relatedSets( $property->id );
        $tables = Table::all();

        return view('citynexus::property.show', compact('property', 'datasets', 'tables'));
    }

    public function getProperties()
    {
        $properties = Property::all();
        return view('citynexus::index', compact('properties'));
    }

    public function getPropertiesData()
    {
        return Datatables::of(Property::select('*')->get())->make(true);
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

    public function getRiskscoreDatafields(Request $request)
    {
        if($request->get('dataset_id') == '_scores')
        {
            $scores = Score::orderBy('name')->get();
            return view('citynexus::risk-score.scores', compact('scores'));
        }
        else
        {
            $dataset = Table::find($request->get('dataset_id'));
            $scheme = json_decode($dataset->scheme);

            return view('citynexus::risk-score.datafields', compact('dataset', 'scheme'));
        }


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

    public function getRiskscoreHeatmap(Request $request)
    {
        $rs = Score::find($request->get('score_id'));
        $scores = Score::all();

        $table = 'citynexus_scores_' . $rs->id;

        $data = DB::table($table)
            ->where('score', '>', '0')
            ->join('citynexus_properties', 'citynexus_properties.id', '=', 'property_id')
            ->select($table . '.property_id', $table . '.score', 'citynexus_properties.lat', 'citynexus_properties.long')
            ->get();

        return view('citynexus::reports.maps.heatmap', compact('rs', 'scores', 'data'));

    }

    public function getAjaxScores(Request $request)
    {
        $score = Score::find($request->get('score_id'));
        $table = 'citynexus_scores_' . $score->id;

        $scores = null;

        if($request->get('type') == 'heatmap')
        {
            $scores = DB::table($table)
                ->where('score', '>', '0')
                ->join('citynexus_properties', 'citynexus_properties.id', '=', 'property_id')
                ->select($table . '.property_id', $table . '.score', 'citynexus_properties.lat', 'citynexus_properties.long')
                ->get();
        }

        return $scores;
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

        // test if there is a score_id
        if($request->get('score_id') != null)
        {
            $score = Score::find($request->get('score_id'));
        }

        // If no score_id create a new score
        else
        {
            $score = new Score;
        }

        // Encode elements array
        $elements = array();
        foreach($request->get('elements') as $i)
        {
            $elements[] = json_decode($i);
        }

        $score->name = $request->get('name');
        $score->elements = json_encode($elements);
        $score->status = 'pending';
        $score->save();

        $this->runScore($score, $elements);

        return redirect( config('citynexus.root_directory') . '/risk-score/scores' );
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

    public function getEditScore(Request $request)
    {
        $score = Score::find($request->get('score_id'));
        $datasets = Table::all();

        return view('citynexus::risk-score.edit', compact('score', 'datasets'));
    }

    public function postUpdateScore(Request $request)
    {
        $score = Score::find($request->get('score_id'));
        $elements = json_decode($score->elements);

        // Queue up to run the score
        $this->runScore($score, $elements);

        // Put score as pending
        $score->status = 'pending';
        $score->save();


        // TODO: Queue up a notification for the users that their score is done and touch the score model.

        return "Success";
    }

    public function getDuplicateScore(Request $request)
    {
        $score = Score::find($request->get('score_id'))->replicate();
        $score->name = $score->name . ' Copy';
        $score->save();

        return redirect('/' . config('citynexus.root_directory') . '/risk-score/edit-score?score_id=' . $score->id );
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
}