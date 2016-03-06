<?php

namespace CityNexus\CityNexus\Http;

use App\Http\Controllers\Controller;
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


class CitynexusController extends Controller
{
    public function getIndex()
    {
        return view('citynexus::index');
    }

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
        return view('citynexus::property.index', compact('properties'));
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

    public function getSettings()
    {
        $app_s = Setting::all();
        $user_s = Setting::where('user_id', Auth::id());

        return view('citynexus::settings.edit', compact('app_s', 'user_s'));

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