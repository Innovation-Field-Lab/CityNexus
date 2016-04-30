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

    public function getScatterChart(Request $request)
    {
        $this->authorize('reports', 'create');
        $datasets = Table::where('table_title', "!=", 'null')->orderBy('table_name')->get(['table_name', 'table_title', 'id']);
        if($request->get('data') == false)
        {
            return view('citynexus::reports.charts.scatter_chart', compact('datasets'));
        }

        else
        {
            $h_data = $request->get('h_data');
            $v_data = $request->get('v_data');
            $data = json_encode($this->getScatterDataSet("tabler_assessor's_department", 'parcelvalue', "tabler_assessor's_department", 'landvalue'));
            return view('citynexus::reports.charts.scatter_chart', compact('datasets', 'data'));

        }

    }

    // Ajax Calls

    public function getDataFields($id, $axis)
    {
        if($id == '_scores')
        {
            $scores = Score::orderBy('name')->get();
            return view('citynexus::reports.charts.scatter._datafields', compact('scores', 'axis'));
        }
        $dataset = Table::find($id);

        $scheme = json_decode($dataset->scheme);

        return view('citynexus::reports.charts.scatter._datafields', compact('dataset', 'scheme', 'axis'));
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