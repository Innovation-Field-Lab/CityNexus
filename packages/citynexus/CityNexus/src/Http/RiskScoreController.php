<?php

namespace CityNexus\CityNexus\Http;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use CityNexus\CityNexus\Property;
use CityNexus\CityNexus\DatasetQuery;
use CityNexus\CityNexus\GenerateScore;
use CityNexus\CityNexus\Score;
use CityNexus\CityNexus\Setting;
use CityNexus\CityNexus\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use CityNexus\CityNexus\Table;
use CityNexus\CityNexus\ScoreBuilder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Facades\Excel;


class RiskScoreController extends Controller
{

    public function getIndex()
    {
        $this->authorize('citynexus', ['group' => 'scores', 'method' => 'view']);
        $scores = Score::orderBy('name')->get();
        return view('citynexus::risk-score.index')
            ->with('scores', $scores);
    }

    public function getCreate()
    {
        $this->authorize('citynexus', ['scores', 'create']);

        $datasets = Table::whereNotNull('table_name')->orderBy('table_name')->get();

        return view('citynexus::risk-score.new', compact('datasets'));
    }

    public function getDataFields(Request $request)
    {
        switch($request->get('dataset_id'))
        {
            case '_scores':
                $scores = Score::orderBy('name')->get();
                return view('citynexus::risk-score.scores', compact('scores'));
                break;
            case'_tags':
                $tags = Tag::orderBy('tag')->get();
                return view('citynexus::risk-score.tags', compact('tags'));
                break;
            default:
                $dataset = Table::find($request->get('dataset_id'));
                $scheme = json_decode($dataset->scheme);
                return view('citynexus::risk-score.datafields', compact('dataset', 'scheme'));
                break;
        }
    }

    public function getUpload()
    {
        return view('citynexus::risk-score.upload');
    }

    public function postUpload(Request $request)
    {
        //get uploaded file
        $file = $request->file('file');

        //turn file into an object
        Excel::load($file, function($reader) use($request) {
            $data = $reader->toArray();

            $error = false;
            if(!isset($data[0]['property_id']))
            {
                $error = true;
                Session::flash('flash_error', "Data set did not include a column header of \"property_id\". Please correct and reupload.");
            }
            if(!isset($data[0]['score']))
            {
                $error = true;
                Session::flash('flash_error', "Data set did not include a column header of \"score\". Please correct and reupload.");
            }
            if($error)
            {
                return redirect()->back();
            }

            $upload = [];
            foreach($data as $row)
            {
                if(isset($row['property_id']) && isset($row['score']))
                {
                    $upload[] = [
                        'property_id' => $row['property_id'],
                        'score' => $row['score']
                    ];
                }
            }

            if($request->exists('score_id'))  {
                $score = Score::find($request->get('score_id'));
                $score->name = $request->get('name');
                $score->save();
            }
            else {
                $score = Score::create(['name' => $request->get('name'), 'scope' => 'custom_upload']);
            }

            if (Schema::hasTable('citynexus_scores_' . $score->id)) {
                DB::table('citynexus_scores_' . $score->id)->truncate();
            } else {
                Schema::create('citynexus_scores_' . $score->id, function (Blueprint $table) {
                    $table->increments('id');
                    $table->integer('property_id');
                    $table->float('score')->nullable();
                });
            }

            DB::table('citynexus_scores_' . $score->id)->insert($upload);

        });

        return redirect()->action('\CityNexus\CityNexus\Http\RiskScoreController@getIndex');

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
            ->whereNotNull($table . '.score')
            ->where($table . '.score', '!=', 0)
            ->orderBy($table . '.score', 'DESC')
            ->get();

        //Return view of properties
        return view('citynexus::reports.ranking', compact('score', 'properties'));
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

        if ($request->get('with_zeros')) {
            $data = DB::table($table)->orderBy('score')->lists('score');
            $min = DB::table($table)->min('score');
        } else {
            $data = DB::table($table)->where('score', '>', 0)->orderBy('score')->lists('score');
            $min = DB::table($table)->where('score', '>', 0)->min('score');
        }
        // Bern view
        $count = count($data);

        if ($request->get('feel') != null) {
            if ($request->get('feel') == 'bern') {
                $bern = $count - ($count / 100);
                $bern = intval($bern);
                $cutoff = $data[$bern];
            }

            if ($request->get('feel') == 'malthus') {
                $malthus = $count - ($count / 20);
                $malthus = intval($malthus);
                $cutoff = $data[$malthus];
            }

            if ($request->get('feel') == 'castro') {
                $castro = $count - ($count / 10);
                $castro = intval($castro);
                $cutoff = $data[$castro];
            }

            $data = DB::table($table)->where('score', '<', $cutoff)->where('score', '>', 0)->orderBy('score')->lists('score');
            $min = DB::table($table)->where('score', '<', $cutoff)->where('score', '>', 0)->min('score');
            $max = $cutoff;
            $count = count($data);
        }

        $zeros = DB::table($table)->where('score', '<=', '0')->count();
        $sum = DB::table($table)->sum('score');
        $middle = $count / 2;
        $firstQ = $count / 4;
        $thirdQ = $middle + $firstQ;
        $bTen = $count / 10;
        $tTen = $count - $bTen;

        $stats = [
            'max' => $max,
            'min' => $min,
            'count' => $count,
            'mean' => $sum / $count,
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

        if ($request->get('type') == 'heatmap') {
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

    public function getUpdateScore($id)
    {

        $score = Score::find($id);
        $this->runScore($score);

        return 'well that worked';
    }

    public function postSaveScore(Request $request)
    {

        $this->validate($request, [
            'name' => 'required|max:255',
            'elements' => 'required'
        ]);

        // test if there is a score_id
        if ($request->get('score_id') != null) {
            $score = Score::find($request->get('score_id'));
        } // If no score_id create a new score
        else {
            $score = new Score;
        }

        // Encode elements array
        $elements = array();
        foreach ($request->get('elements') as $i) {
            $elements[] = json_decode($i);
        }

        $score->name = $request->get('name');
        $score->elements = json_encode($elements);
        $score->status = 'complete';
        $score->save();

        $this->runScore($score);

        return redirect(action('\CityNexus\CityNexus\Http\RiskScoreController@getIndex'));
    }

    public function getEditScore($id)
    {
        $score = Score::find($id);
        $datasets = Table::whereNotNull('table_name')->orderBy('table_name')->get();
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
        } catch (\Exception $e) {
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

        return $this->getEditScore($score->id);
    }

    public function getSettings()
    {
        $app_s = Setting::all();
        $user_s = Setting::where('user_id', Auth::getUser()->id())->get();

        return view('citynexus::settings.edit', compact('app_s', 'user_s'));

    }


    private function runScore($score)
    {

        // get elements in the score
        $elements = \GuzzleHttp\json_decode($score->elements);

        $ignore_ids = [];
        // create array of ignored properties
        foreach ($elements as $k => $i)
        {
            if(isset($i->score_type) && $i->score_type == 'ignore')
            {
                $ids = Tag::find($i->tag_id)->properties->lists('id');
                foreach ($ids as $id)
                {
                    $ignore_ids[$id] = false;
                }

                unset($elements[$k]);
            }
        }

        // create the name of the score
        $table = 'citynexus_scores_' . $score->id;

        if (Schema::hasTable('citynexus_scores_' . $score->id)) {
            DB::table('citynexus_scores_' . $score->id)->truncate();
        } else {
            Schema::create($table, function (Blueprint $table) {
                $table->increments('id');
                $table->integer('property_id');
                $table->float('score')->nullable();
            });
        }

        $properties = array();

        // create an array of all properties with data

        foreach ($elements as $element) {

            switch($element->scope)
            {
                case 'score':
                    $table_name = 'citynexus_scores_' . $element->table_id;
                    $key = 'score';
                    $properties = array_merge($properties, DB::table($table_name)->whereNotNull($key)->whereNotNull('property_id')->lists('property_id'));

                    break;
                case 'tag':
                    $table_name = null;
                    $key = null;
                    $properties = array_merge($properties, DB::table('property_tag')->where('tag_id', $element->tag_id)->whereNull('deleted_at')->lists('property_id'));
                    break;
                default:
                    $table_name = $element->table_name;
                    $key = $element->key;
                    $properties = array_merge($properties, DB::table($table_name)->whereNotNull($key)->whereNotNull('property_id')->lists('property_id'));
                    break;
            }

        }

        $properties = array_unique($properties);        // remove duplicate array values

        sort($properties);                              // sort array by property id value
        if ($properties[0] == '') unset($properties[0]);  //if first element is equal to null, remove

        $insert = array();

        // remove ignore properties and prepare insert
        foreach ($properties as $i) {
            if ($i != null && !isset($ignore_ids[$i])) {
                $insert[] = ['property_id' => $i];
            }
        }

        // insert blank property Ids into db
        DB::table($table)->insert($insert);

        // process based on element score
        foreach ($elements as $element) {
            switch ($element->scope) {

                case 'tag':
                    $this->genByTag($element, $score->id);
                    break;
                case 'last':
                    $this->genByLastElement($element, $score->id);
                    break;
                case 'score':
                    $this->genByLastElement($element, $score->id);
                    break;
                case 'all':
                    $this->genByAllElement($element, $score->id);
                    break;
            }

        }

        // all complete return true;
        return true;
    }

    private function genByLastElement($element, $score_id)
    {

        $scorebuilder = new ScoreBuilder();

        // Check if the score element is based on a score.
        if (null != $element->scope && $element->scope == 'score') {
            $table_name = 'citynexus_scores_' . $element->table_id;
            $key = 'score';

            $values = DB::table($table_name)
                ->whereNotNull($key)
                ->whereNotNull('property_id')
                ->select('property_id', $key)->get();
        } else {
            $table_name = $element->table_name;
            $key = $element->key;

            if ($element->period != null) {
                $today = Carbon::today();

                $values = DB::table($table_name)
                    ->where('updated_at', '>', $today->subDays($element->period))
                    ->whereNotNull($key)
                    ->whereNotNull('property_id')
                    ->orderBy('created_at')
                    ->select('property_id', $key)->get();
            }

        }

        if (!isset($values)) {
            $values = DB::table($table_name)
                ->whereNotNull($key)
                ->orderBy('created_at')
                ->select('property_id', $key)->get();
        }

        $oldscores = DB::table('citynexus_scores_' . $score_id)->select('property_id', 'score')->get();

        $scores = array();

        foreach ($oldscores as $i) {
            $scores[$i->property_id] = [
                'property_id' => $i->property_id,
                'score' => $i->score
            ];
        }


        $new_score = array();
        foreach ($values as $value) {
            if(isset($scores[$value->property_id]))
            {
                $new_score[$value->property_id] = $scorebuilder->calcElement($value->$key, $element);
            }
        }

        foreach ($scores as $i) {

            if (isset($new_score[$i['property_id']])) {
                $updated_score = $i['score'] + $new_score[$i['property_id']];
            } else {
                $updated_score = $i['score'];
            }


            if ($updated_score !== null && $i['property_id'] != null) {
                $upload[] = [
                    'property_id' => $i['property_id'],
                    'score' => $updated_score,
                ];
            }

        }

        DB::table('citynexus_scores_' . $score_id)->truncate();
        DB::table('citynexus_scores_' . $score_id)->insert($upload);

    }

    private function genByAllElement($element, $score_id)
    {
        $key = $element->key;
        $scorebuilder = new ScoreBuilder();


        if ($element->table_name != null) {
            // if there is a table name, use it
            $table_name = $element->table_name;
        } else {
            return false;
        }


        if ($element->period != null) {
            $today = Carbon::today();

            $values = DB::table($table_name)
                ->where('updated_at', '>', $today->subDays($element->period))
                ->whereNotNull($key)
                ->orderBy('created_at')
                ->select('property_id', $key)->get();
        } else {
            $values = DB::table($table_name)
                ->whereNotNull($key)
                ->orderBy('created_at')
                ->select('property_id', $key)->get();
        }
        $oldscores = DB::table('citynexus_scores_' . $score_id)->select('property_id', 'score', 'id')->get();

        $scores = array();

        foreach ($oldscores as $i) {
            $scores[$i->property_id] = [
                'property_id' => $i->property_id,
                'score' => $i->score
            ];
        }


        $sortedvalues = array();

        // create array of values values associated with PID
        foreach ($values as $i) {
            $sortedvalues[$i->property_id][] = $i->$key;
        }

        // Process each property ID
        foreach ($sortedvalues as $pid => $values) {

            if(isset($scores[$pid]))
            {
                // process each value for the property
                foreach ($values as $value) {
                    // Check that the pid exists, if not create a record for it
                    if (!isset($scores[$pid])) {
                        break;
                    }

                    // add new score to existing score
                    $new_score = $scores[$pid]['score'] + $scorebuilder->calcElement($value, $element);

                    // if new_score is not null, create a place holder score
                    if ($new_score !== null) {
                        $scores[$pid] = [
                            'property_id' => $pid,
                            'score' => $new_score,
                        ];

                    }
                }
            }
        }

        DB::table('citynexus_scores_' . $score_id)->truncate();
        DB::table('citynexus_scores_' . $score_id)->insert($scores);
    }

    private function genByTag($element, $score_id)
    {

        // Get all properties with tag
        $tagged = Tag::find($element->tag_id)->properties;

        // Get all existing scores
        $scores = DB::table('citynexus_scores_' . $score_id)->get();

        // create array of score adjustments
        foreach ($tagged as $k => $i)
        {
            if(isset($score_adj[$i->property_id]))
            {
                if($i->score_type == 'add')
                    $score_adj[$i->property_id] = $score_adj[$i->property_id] + $element->factor;
                elseif($i->score_type == 'subtract')
                    $score_adj[$i->property_id] = $score_adj[$i->property_id] - $element->factor;

            }
            else
            {

                if($i->score_type == 'add')
                    $score_adj[$i->property_id] = $element->factor;
                elseif($i->score_type == 'subtract')
                    $score_adj[$i->property_id] = 0 - $element->factor;

            }
        }

        // Loop through properties and adjust scores

        foreach ($scores as $k => $i)
        {
            $new_scores[$k] = ['property_id' => $i->property_id, 'score' => $i->score];
            if(isset($score_adj[$i->property_id]))
            {
                $new_scores[$k]['score'] = $i->score + $score_adj[$i->property_id];
            }
        }
        // Store final scores
        DB::table('citynexus_scores_' . $score_id)->truncate();
        
        DB::table('citynexus_scores_' . $score_id)->insert($new_scores);


    }
}