<?php

namespace CityNexus\CityNexus\Http;

use App\Http\Controllers\Controller;
use CityNexus\CityNexus\Property;
use CityNexus\CityNexus\GenerateScore;
use CityNexus\CityNexus\Report;
use CityNexus\CityNexus\Score;
use CityNexus\CityNexus\Tag;
use CityNexus\CityNexus\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use CityNexus\CityNexus\Geocode;
use CityNexus\CityNexus\Table;
use Illuminate\Support\Facades\Session;


class ViewController extends Controller
{

    public function getIndex()
    {
        $this->authorize('citynexus', ['reports', 'view']);
        $views = View::orderBy('name')->get();
        return view('citynexus::reports.views.index', compact('views'));
    }

    public function getShow( $id )
    {
        $view = View::find( $id );

        switch($view->setting->type) {
            case 'Heat Map':
                $table = Table::find($view->setting->table_id);
                return redirect(action('\CityNexus\CityNexus\Http\ViewController@getHeatMap') . "?table=" . $view->setting->table_name . "&key=" . $view->setting->key . '&view_id=' . $id);
                break;
            case 'Pin Map':
                if(isset($view->setting->tag_id))
                {
                    $pin = Tag::find($view->setting->tag_id);
                    return redirect(action('\CityNexus\CityNexus\Http\TagController@getPinMap', [$pin->id]));
                }
                elseif(isset($view->setting->score_id))
                {
                    $score = Score::find($view->setting->score_id);
                    return redirect(action('\CityNexus\CityNexus\Http\RiskScoreController@getPinMap', [$score->id]));
                }
                  break;
            case 'Distribution':
                return redirect(action('\CityNexus\CityNexus\Http\ViewController@getDistribution', ['table' => $view->setting->table_name, "key" => $view->setting->key]) . '?view_id=' . $id);
                break;
            case 'Scatter Chart':
                return redirect(action('\CityNexus\CityNexus\Http\ViewController@getScatterChart', ['view_id' => $id]));

            case 'Dot Map':
                $table = Table::find($view->setting->table_id);
                return redirect(action('\CityNexus\CityNexus\Http\ViewController@getDotMap') . "?table=" . $view->setting->table_name . "&key=" . $view->setting->key . '&view_id=' . $id);
                break;

            default:
                abort(404);
        }

    }

    public function getScatterChart($view_id = null)
    {
        $this->authorize('citynexus', ['reports', 'view']);
        $datasets = Table::whereNotNull('table_title')->orderBy('table_name')->get(['table_name', 'table_title', 'id', 'scheme']);
        $scores = Score::orderBy('name')->get();
        if($view_id != null)
        {
            $settings = View::find($view_id)->setting;
            return view('citynexus::reports.charts.scatter_chart', compact('datasets', 'scores', 'settings'));
        }
        return view('citynexus::reports.charts.scatter_chart', compact('datasets', 'scores'));

    }


    public function getDistribution($table = null, $key = null, Request $request = null)
    {
        $this->authorize('citynexus', ['reports', 'view']);

        $datasets = Table::where('table_title', "!=", 'null')->orderBy('table_name')->get(['table_name', 'table_title', 'id']);

        if ($table != null && $key != null)
        {

            $max = DB::table($table)->max($key);

            if ($request->get('with_zeros')) {
                $data = DB::table($table)->orderBy($key)->lists($key);
                $min = DB::table($table)->min($key);
            } else {
                $data = DB::table($table)->where($key, '>', 0)->orderBy($key)->lists($key);
                $min = DB::table($table)->where($key, '>', 0)->min($key);
            }
            // Bern view
            $count = count($data);

            if ($request->get('feel') != null) {
                $feel = $request->get('feel');
                switch($feel){
                    case 'bern':
                        $bern = $count - ($count / 100);
                        $bern = intval($bern);
                        $cutoff = $data[$bern];
                        break;
                    case 'malthus':
                        $malthus = $count - ($count / 20);
                        $malthus = intval($malthus);
                        $cutoff = $data[$malthus];
                        break;
                    case 'castro':
                        $castro = $count - ($count / 10);
                        $castro = intval($castro);
                        $cutoff = $data[$castro];
                        break;
                }
                $data = DB::table($table)->where($key, '<', $cutoff)->where($key, '>', 0)->orderBy($key)->lists($key);
                $min = DB::table($table)->where($key, '<', $cutoff)->where($key, '>', 0)->min($key);
                $max = $cutoff;
                $count = count($data);
            }

            $zeros = DB::table($table)->where($key, '<=', '0')->count();
            $sum = DB::table($table)->sum($key);
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

            if(substr($table, 0, 17) == 'citynexus_scores_')
            {
                $key_name = $key;
                $table_name = $table;
            }
            else {
                $table_ob = Table::where('table_name', $table)->first();
                $schema = $table_ob->schema;
                $key_name = $schema->$key->name;
                $table_name = $table_ob->table_name;
            }

            return view('citynexus::reports.charts.distribution_curve', compact('data', 'stats','table_name', 'key_name', 'table', 'key'));
        }

        else{
            $distribution = true;
            return view('citynexus::reports.charts.distribution_curve', compact('datasets', $distribution));

        }
    }

    public function getHeatMap(Request $request)
    {
        $this->authorize('citynexus', ['reports', 'view']);

        $datasets = Table::whereNotNull('table_title')->orderBy('table_title')->get();

        if($request->get('table') && $request->get('key'))
        {
            if(fnmatch('citynexus_scores_*', $request->get('table')))
            {
                $scores = Score::whereNotNull('name')->orderBy('name')->get(['id', 'name']);
                return view('citynexus::reports.maps.heatmap', compact('datasets', 'scores', 'view_id'))
                    ->with('table', $request->get('table'))
                    ->with('key', $request->get('key'));
            }
            $dataset = Table::where('table_name', $request->get('table'))->first();
            $scheme = $dataset->schema;
            $view_id = null;
            if($request->get('view_id') != null) {
                $viewt_id = $request->get('view_id');
            }
            return view('citynexus::reports.maps.heatmap', compact('datasets', 'dataset', 'scheme', 'view_id'))
                ->with('table', $request->get('table'))
                ->with('key', $request->get('key'));
        }
        else{
            return view('citynexus::reports.maps.heatmap', compact('datasets'));
        }
    }

    public function getDotMap(Request $request)
    {
        $this->authorize('citynexus', ['reports', 'view']);


        $datasets = Table::whereNotNull('table_title')->orderBy('table_title')->get();
        $scores = Score::all();


        $table = $request->get('table');
        $key = $request->get('key');

        if($request->get('table') != null && $request->get('key') !=null)
            return view('citynexus::reports.maps.dotmap', compact('datasets', 'table', 'key', 'scores'));

        else
            return view('citynexus::reports.maps.dotmap', compact('datasets', 'scores'));
    }

    public function postDotMap(Request $request)
    {
        switch ($request->get('type'))
        {
            case 'dataset':
                return $this->createDataset($request);
                break;

            case 'datapoint':
                return $this->createDatapoint($request);
                break;

            case 'score':
                return $this->createScoreData($request);
                break;

            case 'tag':
                break;

            default:
                return response(500, 'No such data');
        }
    }

    private function createDatapoint($request)
    {

        $dataset = Table::find($request->get('dataset_id'));
        $table = $dataset->table_name;
        $key = $request->get('key');

        $results = DB::table($table)
            ->where($key, '>', '0')
            ->orderBy($table . '.created_at')
            ->join('citynexus_properties', $table . '.property_id', '=', 'citynexus_properties.id')
            ->select($table . '.' . $key, 'citynexus_properties.id', 'citynexus_properties.full_address', 'citynexus_properties.lat', 'citynexus_properties.long')
            ->get();

        $max = 0;

        foreach($results as $i)
        {
            if($i->lat != null && $i->long != null)
            {
                $points[$i->id] = [
                    'name' => ucwords($i->full_address),
                    'value' => $i->$key,
                    'url' => action('\CityNexus\CityNexus\Http\PropertyController@getShow', [$i->id]),
                    'lat' => $i->lat,
                    'lng' => $i->long,
                ];
            }
            if($max < $i->$key) $max = $i->$key;
        }

        $points = $this->createInfoFlags($points);

        $return['points'] = array_values($points);
        $return['max'] = $max * 1.1;
        $return['title'] = $dataset->table_title . " > " . $dataset->schema->$key->name;
        $return['handle'] = $dataset->id . '_' . $key;

        return $return;
    }


    private function createDataset($request)
    {
        $dataset = Table::find($request->get('dataset_id'));
        $table = $dataset->table_name;

        $results = DB::table($table)
            ->join('citynexus_properties', $table . '.property_id', '=', 'citynexus_properties.id')
            ->select($table . '.id', 'citynexus_properties.id', 'citynexus_properties.full_address', 'citynexus_properties.lat', 'citynexus_properties.long')
            ->get();

        $max = 0;

        $points = [];

        foreach($results as $i)
        {

            if($i->lat != null && $i->long != null)
            {
                if(isset($points[$i->id]))
                {
                    $points[$i->id]['value'] = $points[$i->id]['value'] + 1;
                }
                else{
                    $points[$i->id] = [
                        'name' => ucwords($i->full_address),
                        'value' => 1,
                        'url' => action('\CityNexus\CityNexus\Http\PropertyController@getShow', [$i->id]),
                        'lat' => $i->lat,
                        'lng' => $i->long,
                    ];
                }
                if($max < $points[$i->id]['value']) $max = $points[$i->id]['value'];

            }
        }

        $points = $this->createInfoFlags($points);

        $return['points'] = array_values($points);
        $return['max'] = $max * 1.1;
        $return['title'] = 'Record Count: ' . $dataset->table_title;
        $return['handle'] = 'table_' . $dataset->id;

        return $return;
    }

    private function createScoreData($request)
    {
        $score = Score::find($request->get('id'));
        $table = 'citynexus_scores_' . $score->id;

        $results = DB::table($table)
            ->join('citynexus_properties', $table . '.property_id', '=', 'citynexus_properties.id')
            ->select($table . '.score', 'citynexus_properties.id', 'citynexus_properties.full_address', 'citynexus_properties.lat', 'citynexus_properties.long')
            ->get();

        $max = 0;

        $points = [];

        foreach($results as $i)
        {

            if($i->lat != null && $i->long != null)
            {
                $points[] = [
                    'name' => ucwords($i->full_address),
                    'value' => $i->score,
                    'url' => action('\CityNexus\CityNexus\Http\PropertyController@getShow', [$i->id]),
                    'lat' => $i->lat,
                    'lng' => $i->long,
                ];

                if($max < $i->score) $max = $i->score;

            }
        }

        $points = $this->createInfoFlags($points);

        $return['points'] = array_values($points);
        $return['max'] = $max * 1.1;
        $return['title'] = 'Property Score: ' . $score->name;
        $return['handle'] = 'score_' . $score->id;

        return $return;
    }

    private function createInfoFlags($points)
    {
        $return = [];

        foreach($points as $point)
        {
            if(isset($return[$point['lat'] . '-' . $point['lng']]['value']))
            {
                $return[$point['lat'] . '-' . $point['lng']]['value'] = ($return[$point['lat'] . '-' . $point['lng']]['value'] / $return[$point['lat'] . '-' . $point['lng']]['count']) + ($point['value'] / $return[$point['lat'] . '-' . $point['lng']]['count']) ;
                $return[$point['lat'] . '-' . $point['lng']]['message'] = $return[$point['lat'] . '-' . $point['lng']]['message'] . '(' . $point['value'] . ')' . ' - <a href="' . $point['url'] . '" target="_blank">' . $point['name'] . '</a></br>';
                $return[$point['lat'] . '-' . $point['lng']]['count'] = $return[$point['lat'] . '-' . $point['lng']]['count'] + 1;
            }
            else
            {
                $return[$point['lat'] . '-' . $point['lng']]['value'] = $point['value'];
                $return[$point['lat'] . '-' . $point['lng']]['count'] = 1;
                $return[$point['lat'] . '-' . $point['lng']]['lat'] = $point['lat'];
                $return[$point['lat'] . '-' . $point['lng']]['lng'] = $point['lng'];
                $return[$point['lat'] . '-' . $point['lng']]['message'] = '(' . $point['value'] . ')' . ' - <a href="' . $point['url'] . '" target="_blank">' . $point['name'] . '</a></br>';
            }
        }

        return $return;
    }

    // Ajax Calls

    public function getDataFields($id, $axis = null, $type = null)
    {
        if($id == '_scores')
        {
            $scores = Score::orderBy('name')->get();

            if($type != null)
            {
                return view('citynexus::reports.includes.' .  $type . '._datafields', compact('scores', 'scheme'));
            }

            return view('citynexus::reports.includes.scatter._datafields', compact('scores', 'axis'));

        }
        $dataset = Table::find($id);

        $scheme = $dataset->schema;

        if($type != null)
        {
            return view('citynexus::reports.includes.' . $type . '._datafields', compact('dataset', 'scheme'));

        }

        return view('citynexus::reports.includes.scatter._datafields', compact('dataset', 'scheme', 'axis'));
    }

    public function getHeatMapData($table, $key)
    {
        $raw_data = DB::table($table)
            ->where( $key, '>', '0')
            ->join('citynexus_properties', 'citynexus_properties.id', '=', 'property_id')
            ->join('citynexus_locations', 'citynexus_locations.id', '=', 'citynexus_properties.location_id')
            ->whereNull('citynexus_properties.deleted_at')
            ->whereNotNull('citynexus_properties.location_id')
            ->whereNotNull('citynexus_locations.lat')
            ->whereNotNull('citynexus_locations.long')
            ->select('citynexus_locations.lat', 'citynexus_locations.long', $table . '.' . $key)
            ->get();

        $max = DB::table($table)
            ->max($key);

        foreach($raw_data as $i)
        {
            $data[] =[$i->lat, $i->long, $i->$key/$max];
        }

        return $data;
    }

    public function postScatterDataSet(Request $request)
    {
        $ver = $this->getAxis($request->get('ver'));
        $hor = $this->getAxis($request->get('hor'));

        $pids = array_keys($hor);


        $property = current((array) Property::find($pids)->keyBy('id'));

        $results = [];

        foreach($hor as $key => $i)
        {
            if(isset($ver[$key]) && isset($property[$key]))
            {
                $results[] = [
                    'x' => $i,
                    'y' => $ver[$key],
                    'property_id' => $key,
                    'full_address' => $property[$key]->full_address
                ];

            }
        }

        return $results;
    }

    private function getAxis($settings)
    {
        switch ($settings['dataset'])
        {
            case '_score':
                return $this->axisScore($settings);
                break;

            default:
                return $this->axisDataset($settings);
                break;
        }

    }

    private function axisScore($settings)
    {
        $data = DB::table('citynexus_scores_' . $settings['key'])->get(['property_id','score']);

        $return = [];

        foreach($data as $i) $return[$i->property_id] = $i->score;

        return $return;
    }

    private function axisDataset($settings)
    {
        switch ($settings['scope'])
        {
            case 'mean':
                return $this->axisMean($settings);
                break;
            case 'most-recent':
                return $this->axisLatest($settings);
                break;
            case 'sum':
                return $this->axisSum($settings);
                break;
            default:
                return $this->axisCount($settings);
                break;
        }
    }


    private function axisMean($settings)
    {
        $table = Table::find($settings['dataset']);

        $data = DB::table($table->table_name)->whereNotNull($settings['key'])->get(['property_id', $settings['key']]);

        $sets = [];

        $key = $settings['key'];
        foreach($data as $i)
        {
            $sets[$i->property_id][] = $i->$key;
        }

        $return = [];
        foreach($sets as $k => $i)
        {
            if($k != null) $return[$k] = array_sum($i) / count($i);
        }

        return $return;
    }

    private function axisSum($settings)
    {
        $table = Table::find($settings['dataset']);

        $data = DB::table($table->table_name)->whereNotNull($settings['key'])->get(['property_id', $settings['key']]);

        $sets = [];

        $key = $settings['key'];
        foreach($data as $i)
        {
            $sets[$i->property_id][] = $i->$key;
        }

        $return = [];
        foreach($sets as $k => $i)
        {
            if($k != null) $return[$k] = array_sum($i);
        }

        return $return;
    }

    private function axisCount($settings)
    {
        $table = Table::find($settings['dataset']);

        $data = DB::table($table->table_name)->whereNotNull($settings['key'])->whereNotNull('property_id')->get(['property_id', $settings['key']]);

        $sets = [];

        $key = $settings['key'];
        foreach($data as $i)
        {
            $sets[$i->property_id][] = $i->$key;
        }

        $return = [];
        foreach($sets as $k => $i)
        {
            if($k != null) $return[$k] = count($i);
        }

        return $return;
    }

    private function axisLatest($settings)
    {
        $table = Table::find($settings['dataset']);

        $data = DB::table($table->table_name)->whereNotNull($settings['key'])->whereNotNull('property_id')->orderBy('created_at', 'DESC')->get(['property_id', $settings['key']]);

        $key = $settings['key'];

        $return = [];
        foreach($data as $k => $i)
        {
            if(!isset($return[$i->property_id])) $return[$i->property_id] = $i->$key;
        }

        return $return;
    }


    public function getScatterDataSet($h_tablename, $h_key, $v_tablename, $v_key )
    {
        $return = null;

        // Build Horizontal Axis
        $horizontal = array_filter($this->getDataSet($h_tablename, $h_key));


        // Build Vertical Axis

        $vertical = array_filter($this->getDataSet($v_tablename, $v_key));

        // Build Combined Data

        $properties = array_filter(array_keys($horizontal));

        $properties = Property::find($properties);

        foreach($horizontal as $k => $i)
        {
            if(isset($vertical[$k])) {

                if(isset($properties->find($k)->full_address))
                {
                    $address = $properties->find($k)->full_address;
                }
                else
                {
                    $address = null;
                }

                $return[] = [
                    'full_address' => $address,
                    'property_id' => $k,
                    'x' => floatval($i),
                    'y' => floatval($vertical[$k])
                ];


            }
        }

        if(count($return) > 0) return $return;

        else return false;

    }

    private function getDataSet( $table_name, $key )
    {
        $return = null;

        $query_results = DB::table($table_name)->orderBy('created_at', 'desc')->lists($key, 'property_id');

        $return = $this->byPropertyId($query_results);

        return $return;
    }

    /**
     * @param $data array
     */
    private function byPropertyId($data)
    {
        $aliases = Property::whereNotNull('alias_of')->lists('id', 'alias_of');


        foreach($data as $k => $i)
        {
            if(isset($aliases->$k))
            {
                $return[$aliases->$k] = $i;
            }
            else
            {
                $return[$k] = $i;
            }
        }

        return $return;
    }

    public function postSaveView(Request $request)
    {
        if($request->get('id') == null)
        {
            if(View::where('name', $request->get('name'))->count() > 0)
            {
                $name = $request->get('name') . ' (' . View::where('name', $request->get('name'))->count() . ")";
            }
            else{
                $name = $request->get('name');
            }
            $view = View::create(['name' => $name, 'settings' => json_encode($request->get('settings'))]);
        }
        else
        {

            $view = Views::find($request->id);
            $view->settings = json_encode($request->get('settings'));
            $view->save();
        }

        return '<a onclick="updateView(' . $view->id . ')" id="save-view" style="cursor: pointer"> Save View Updates</a>';
    }

    public function getDelete($id)
    {
        $this->authorize('citynexus', ['reports', 'delete']);
        View::find($id)->delete();
        Session::flash('flash_success', 'View deleted.');
        return redirect(action('\CityNexus\CityNexus\Http\ViewController@getIndex'));
    }

    private function correlation($x, $y){

        $length= count($x);
        $mean1=array_sum($x) / $length;
        $mean2=array_sum($y) / $length;

        $a=0;
        $b=0;
        $axb=0;
        $a2=0;
        $b2=0;

        for($i=0;$i<$length;$i++)
        {
            $a=$x[$i]-$mean1;
            $b=$y[$i]-$mean2;
            $axb=$axb+($a*$b);
            $a2=$a2+ pow($a,2);
            $b2=$b2+ pow($b,2);
        }

        $corr= $axb / sqrt($a2*$b2);

        return $corr;
    }
}