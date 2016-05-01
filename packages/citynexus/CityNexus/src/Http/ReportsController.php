<?php

namespace CityNexus\CityNexus\Http;

use App\Http\Controllers\Controller;
use App\User;
use CityNexus\CityNexus\GeocodeJob;
use CityNexus\CityNexus\Property;
use CityNexus\CityNexus\DatasetQuery;
use CityNexus\CityNexus\GenerateScore;
use CityNexus\CityNexus\Score;
use CityNexus\CityNexus\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use CityNexus\CityNexus\Table;
use CityNexus\CityNexus\ScoreBuilder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Queue;
use CityNexus\CityNexus\Geocode;
use CityNexus\CityNexus\Tag;


class ReportsController extends Controller
{

    public function getScatterChart()
    {
        $this->authorize('reports', 'create');
        $datasets = Table::where('table_title', "!=", 'null')->orderBy('table_name')->get(['table_name', 'table_title', 'id']);
        return view('citynexus::reports.charts.scatter_chart', compact('datasets'));

    }

    public function getDistributionCurve($table = null, $key = null, Request $request = null)
    {
        $this->authorize('reports', 'create');

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

            return view('citynexus::reports.charts.distribution_curve', compact('data', 'stats'));
        }

        else{
            $distribution = true;
            return view('citynexus::reports.charts.distribution_curve', compact('datasets', $distribution));

        }
    }

    public function getHeatMap(Request $request)
    {
        $datasets = Table::whereNotNull('table_title')->orderBy('table_title')->get();
        if($request->get('table') && $request->get('key'))
        {
            return view('citynexus::reports.maps.heatmap', compact('datasets'))
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

        $scheme = json_decode($dataset->scheme);

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
            ->whereNotNull('citynexus_properties.lat')
            ->whereNotNull('citynexus_properties.long')
            ->select('citynexus_properties.lat', 'citynexus_properties.long', $table . '.' . $key)
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
                    'x' => $i,
                    'y' => $vertical[$k]];
            }
        }

        return $return;

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
}