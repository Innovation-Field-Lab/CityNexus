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

            default:
                abort(404);
        }

    }

    public function getScatterChart($view_id = null)
    {
        $this->authorize('citynexus', ['reports', 'view']);
        $datasets = Table::where('table_title', "!=", 'null')->orderBy('table_name')->get(['table_name', 'table_title', 'id']);
        if($view_id != null)
        {
            $settings = View::find($view_id)->setting;
            return view('citynexus::reports.charts.scatter_chart', compact('datasets', 'settings'));
        }
        return view('citynexus::reports.charts.scatter_chart', compact('datasets'));

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

    public function getScatterDataSet($h_tablename, $h_key, $v_tablename, $v_key )
    {
        $return = null;

        // Build Horizontal Axis
        $horizontal = array_filter($this->getDataSet($h_tablename, $h_key));

        // Build Vertical Axis

        $vertical = array_filter($this->getDataSet($v_tablename, $v_key));


        // Build Combined Data

        $properties = Property::all()->lists('full_address', 'id');

        foreach($horizontal as $k => $i)
        {
            if(isset($vertical[$k])) {

                $return[] = [
                    'address' => $properties[$k],
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
}