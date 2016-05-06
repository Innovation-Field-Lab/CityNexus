<?php

namespace CityNexus\CityNexus\Http;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use CityNexus\CityNexus\Property;
use CityNexus\CityNexus\DatasetQuery;
use CityNexus\CityNexus\GenerateScore;
use CityNexus\CityNexus\Score;
use CityNexus\CityNexus\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use CityNexus\CityNexus\Table;
use CityNexus\CityNexus\ScoreBuilder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Session;


class RiskScoreController extends Controller
{

    public function getIndex()
    {
        $this->authorize('citynexus', ['group' => 'datasets', 'method' => 'view']);

        return view('citynexus::risk-score.index')
            ->with('scores', Score::all());
    }

    public function getCreate()
    {
        $this->authorize('citynexus', ['datasets', 'view']);

        $datasets = Table::all();
        return view('citynexus::risk-score.new', compact('datasets'));
    }

    public function getDataFields(Request $request)
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

    public function getDataField(Request $request)
    {
        $dataset = Table::find($request->get('table_id'));
        $scheme = json_decode($dataset->scheme, false);
        $key = $request->get('key');
        $field = $scheme->$key;
        unset($dataset['_token']);
        return view('citynexus::risk-score.fieldsettings', compact('dataset', 'scheme', 'field'));
    }

    public function getRanking($id)
    {

        $this->authorize('citynexus', ['scores', 'view']);

        // Get Score Model

        $score = Score::find($id);

        // Create pivot table of properties with scores

        $table = 'citynexus_scores_' . $score->id;

        $properties = DB::table($table)
            ->join('citynexus_properties', 'citynexus_properties.id', '=', 'property_id')
            ->select($table . '.property_id', $table . '.score', 'citynexus_properties.full_address')
            ->get();

        //Return view of properties
        return view('citynexus::reports.ranking', compact('score', 'properties'));
    }

    public function getHeatMap($id)
    {
        $this->authorize('citynexus', ['scores', 'view']);

        $rs = Score::find($id);
        $scores = Score::all();

        $table = 'citynexus_scores_' . $rs->id;

        $raw_data = DB::table($table)
            ->where('score', '>', '0')
            ->whereNotNull('lat')
            ->whereNotNull('long')
            ->join('citynexus_properties', 'citynexus_properties.id', '=', 'property_id')
            ->select($table . '.property_id', $table . '.score', 'citynexus_properties.lat', 'citynexus_properties.long')
            ->get();

        $max = DB::table($table)
            ->max('score');


        return view('citynexus::reports.maps.heatmap', compact('rs', 'scores', 'data', 'max'));

    }

    public function getPinMap($id)
    {
        $rs = Score::find($id);
        $scores = Score::all();

        $table = 'citynexus_scores_' . $rs->id;

        $pins = DB::table($table)
            ->where('score', '>', '0')
            ->join('citynexus_properties', 'citynexus_properties.id', '=', 'property_id')
            ->select('citynexus_properties.id', 'citynexus_properties.full_address', $table . '.score', 'citynexus_properties.lat', 'citynexus_properties.long')
            ->get();

        return view('citynexus::reports.maps.pinmap', compact('rs', 'scores', 'pins'));

    }

    public function getDistribution($id, Request $request)
    {
        $rs = Score::find($id);
        $table = 'citynexus_scores_' . $rs->id;
        $max = DB::table($table)->max('score');

        if($request->get('with_zeros'))
        {
            $data = DB::table($table)->orderBy('score')->lists('score');
            $min = DB::table($table)->min('score');
        }
        else
        {
            $data = DB::table($table)->where('score', '>', 0)->orderBy('score')->lists('score');
            $min = DB::table($table)->where('score', '>', 0)->min('score');
        }
        // Bern view
        $count = count($data);

        if($request->get('feel') != null)
        {
            if($request->get('feel') == 'bern')
            {
                $bern = $count - ($count/100);
                $bern = intval ($bern);
                $cutoff = $data[$bern];
            }

            if($request->get('feel') == 'malthus')
            {
                $malthus = $count - ($count/20);
                $malthus = intval ($malthus);
                $cutoff = $data[$malthus];
            }

            if($request->get('feel') == 'castro')
            {
                $castro = $count - ($count/10);
                $castro = intval ($castro);
                $cutoff = $data[$castro];
            }

            $data = DB::table($table)->where('score', '<', $cutoff)->where('score', '>', 0)->orderBy('score')->lists('score');
            $min = DB::table($table)->where('score', '<', $cutoff)->where('score', '>', 0)->min('score');
            $max = $cutoff;
            $count = count($data);
        }

        $zeros = DB::table($table)->where('score', '<=', '0')->count();
        $sum = DB::table($table)->sum('score');
        $middle = $count/2;
        $firstQ = $count/4;
        $thirdQ = $middle + $firstQ;
        $bTen = $count/10;
        $tTen = $count - $bTen;

        $stats = [
            'max' => $max,
            'min' => $min,
            'count' => $count,
            'mean' => $sum/$count,
            'bTen' => $bTen,
            'firstQ' => $firstQ,
            'median' => $middle,
            'thirdQ' => $thirdQ,
            'tTen' => $tTen,
            'zeros' => $zeros,

        ];

        return view('citynexus::reports.charts.normal_distro', compact('data', 'stats', 'rs'));

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
        $score->status = 'complete';
        $score->save();

        $this->runScore($score, $elements);

        return redirect( config('citynexus.root_directory') . '/risk-score/scores' );
    }

    public function getEditScore($id)
    {
        $score = Score::find($id);
        $datasets = Table::all();

        return view('citynexus::risk-score.edit', compact('score', 'datasets'));
    }

    /**
     * @param Request $request
     * @return string
     */
    public function postUpdateScore(Request $request)
    {
        $score = Score::find($request->get('score_id'));
        $elements = json_decode($score->elements);

        // Run the score
        $this->runScore($score, $elements);

        $score->status = 'complete';
        $score->touch();
        $score->save();

        return "Success";
    }

    public function getRemoveScore($id)
    {
        $score = Score::find($id);
        try {
            Schema::drop('citynexus_scores_' . $score->id);
        }
        catch(\Exception $e)
        {
            Session::flash('flash_warning', "Something went wrong. " . $e);
            return redirect()->back();
        }
            $score->delete();
            Session::flash('flash_success', "Score has been successfully removed");

        return redirect()->back();

    }

    public function getDuplicateScore($id)
    {
        $score = Score::find($id)->replicate();
        $score->name = $score->name . ' Copy';
        $score->save();

        return redirect('/' . config('citynexus.root_directory') . '/risk-score/edit-score?score_id=' . $score->id );
    }

    public function getSettings()
    {
        $app_s = Setting::all();
        $user_s = Setting::where('user_id', Auth::getUser()->id())->get();

        return view('citynexus::settings.edit', compact('app_s', 'user_s'));

    }


    private function runScore($score, $elements)
    {

        // Find tables in Score
        $elements = \GuzzleHttp\json_decode($score->elements);

        $table = 'citynexus_scores_' . $score->id;

        if( Schema::hasTable('citynexus_scores_' . $score->id) )
        {
            Schema::drop('citynexus_scores_' . $score->id);
        }

        Schema::create($table, function (Blueprint $table) {
            $table->increments('id');
            $table->integer('property_id');
            $table->float('score')->nullable();
        });

        $properties = array();

        foreach($elements as $element)
        {
            if(null != $element->scope && $element->scope == 'score')
            {
                $table_name = 'citynexus_scores_' . $element->table_id;

                $key = 'score';

            }
            else
            {
                $table_name = $element->table_name;
                $key = $element->key;
            }

            $properties = array_merge( $properties, DB::table($table_name)->whereNotNull($key)->lists('property_id'));
        }

        $properties = array_unique($properties);

        $insert = array();

        foreach($properties as $i)
        {
            $insert[] = ['property_id' => $i];
        }

        DB::table($table)->insert($insert);

        $aliases = DB::table('citynexus_properties')->whereNotNull('alias_of')->select('id', 'alias_of')->get();
        foreach($aliases as $i)
        {
            $alias[$i->id] = $i->alias_of;
        }

        foreach ($elements as $element)
        {
            if($element->scope == 'last') $this->genByLastElement($element,  $score->id, $alias);
            if($element->scope == 'all') $this->genByAllElement($element,  $score->id, $alias);
        }
    }

    private function genByLastElement($element, $score_id, $alias)
    {
        $key = $element->key;
        $scorebuilder = new ScoreBuilder();

        if(null != $element->scope && $element->scope == 'score')
        {
            $table_name = 'citynexus_score_' . $element->table_id;
        }
        else
        {
            $table_name = $element->table_name;
        }


        if($element->period != null)
        {
            $today = Carbon::today();

            $values = DB::table($table_name)
                ->where('updated_at', '>', $today->subDays($element->period))
                ->whereNotNull($key)
                ->orderBy('created_at')
                ->select('property_id', $key)->get();
        }
        else
        {
            $values = DB::table($table_name)
                ->whereNotNull($key)
                ->orderBy('created_at')
                ->select('property_id', $key)->get();
        }

        $oldscores = DB::table('citynexus_scores_' . $score_id)->select('property_id', 'score')->get();

        $scores = array();

        foreach($oldscores as $i)
        {
            $scores[$i->property_id] = [
                'property_id' => $i->property_id,
                'score' => $i->score
            ];
        }

        $new_score = array();
        foreach($values as $value)
        {
            if(isset($alias[$value->property_id]))
            {
                $pid = $alias[$value->property_id];
            }
            else
            {
                $pid = $value->property_id;
            }
            if(!isset($scores[$pid]))
            {
                $scores[$pid] = ['score' => null, 'property_id' => $pid];
            }

            $new_score[$pid] = $scorebuilder->calcElement($value->$key, $element);

        }

        foreach($scores as $i)
        {

            if(isset($new_score[$i['property_id']]))
            {
                $updated_score = $i['score'] + $new_score[$i['property_id']];
            }
            else
            {
                $updated_score = $i['score'];
            }


            if($updated_score === 0 | $updated_score > 0)
            {
                $upload[] = [
                    'property_id' => $i['property_id'],
                    'score' => $updated_score,
                ];
            }

        }

        DB::table('citynexus_scores_' . $score_id)->truncate();
        DB::table('citynexus_scores_' . $score_id)->insert($upload);

    }

    private function genByAllElement($element, $score_id, $alias)
    {
        $key = $element->key;
        $scorebuilder = new ScoreBuilder();

        if(null != $element->scope && $element->scope == 'score')
        {
            $table_name = 'citynexus_score_' . $element->table_id;
        }
        else
        {
            $table_name = $element->table_name;
        }

        if($element->period != null)
        {
            $today = Carbon::today();

            $values = DB::table($table_name)
                ->where('updated_at', '>', $today->subDays($element->period))
                ->whereNotNull($key)
                ->orderBy('created_at')
                ->select('property_id', $key)->get();
        }
        else
        {
            $values = DB::table($table_name)
                ->whereNotNull($key)
                ->orderBy('created_at')
                ->select('property_id', $key)->get();
        }
        $oldscores = DB::table('citynexus_scores_' . $score_id)->select('property_id', 'score', 'id')->get();

        $scores = array();

        foreach($oldscores as $i)
        {
            $scores[$i->property_id] = [
                'property_id' => $i->property_id,
                'score' => $i->score
            ];
        }

        $sortedvalues = array();

        foreach($values as $i)
        {
            $pid = $i->property_id;

            if(isset($alias[$i->property_id]))
            {
                $pid = $alias[$i->property_id];

            }

            $sortedvalues[$pid][] = $i->$key;
        }

        foreach($sortedvalues as $pid => $values)
        {

            if(isset($alias[$pid]))
            {
                $pid = $alias[$pid];
            }

            if(!isset($scores[$pid]))
            {
                $scores[$pid] = ['score' => null];
            }


            foreach($values as $value)
            {
                if(!isset($scores[$pid]))
                {
                    $scores[$pid] = ['score' => null];
                }

                $new_score = $scores[$pid]['score'] + $scorebuilder->calcElement($value, $element);

                if($new_score === 0 | $new_score > 0)
                {
                    $scores[$pid] = [
                        'property_id' => $pid,
                        'score' => $new_score,
                    ];

                }

            }
        }

        DB::table('citynexus_scores_' . $score_id)->truncate();
        DB::table('citynexus_scores_' . $score_id)->insert($scores);
    }
}