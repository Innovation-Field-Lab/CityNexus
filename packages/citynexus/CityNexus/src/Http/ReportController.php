<?php
namespace CityNexus\CityNexus\Http;
use App\Http\Controllers\Controller;
use CityNexus\CityNexus\Property;
use CityNexus\CityNexus\GenerateScore;
use CityNexus\CityNexus\Report;
use CityNexus\CityNexus\Score;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use CityNexus\CityNexus\Geocode;
use CityNexus\CityNexus\Table;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
class ReportController extends Controller
{
    public function getCreateProperty()
    {
        $this->authorize('citynexus', ['reports', 'create']);
        $datasets = Table::whereNotNull('table_name')->get();
        $tables = new Table();
        return view('citynexus::reports.property.create', compact('datasets', 'tables'));
    }
    public function postCreateProperty(Request $request)
    {
        $report = $request->all();
        $report['settings_json'] = json_encode($report['settings']);
        Report::create($report);
        Session::flash('flash_success', 'Report Saved');
        return redirect('/');
    }
    public function getPropertyReport($report_id, $property_id)
    {
        $report = Report::find($report_id);
        $property = Property::find($property_id);
        foreach($report->settings as $key => $setting)
        {
            switch($key)
            {
                case 'property_info':
                    $report_info['property_info'] = $this->propertyInfo($setting, $property);
                    break;
                case 'datasets':
                    $datasets = $this->datasetElements($setting, $property_id);
                    if($datasets != null) $report_info['datasets'] = $datasets;
                    break;
                default:
                    null;
            }
        }
        return view('citynexus::reports.property.view', compact('report_info', 'property', 'report'));
    }
    private function propertyInfo($setting, $property)
    {
        $return = [];
        foreach($setting as $key => $item)
        {
            switch($key)
            {
                case "full_address":
                    $return["full_address"] = $property->full_address;
                    break;
                case 'citynexus_id':
                    $return['citynexus_id'] = $property->id;
                    break;
                case 'geocoordinates':
                    $location = $property->location;
                    $return["geocoordinates"] = 'Lat: ' . $location->lat. ', Long: ' . $property->long;
                    break;
                default:
                    null;
            }
        }
        return $return;
    }
    private function datasetElements($setting, $property_id)
    {
        $return = [];
        foreach($setting as $table_name => $columns)
        {
            $data = DB::table($table_name)->where('property_id', $property_id)->get();
            $rows = [];
            foreach($data as $item)
            {
                $row = [];
                foreach($columns as $column => $i)
                {
                    $row[$column] = $item->$column;
                }
                $rows[] = $row;
            }
            if($rows != null) $return[$table_name] = $rows;
        }
        return $return;
    }

    public function getPivotIndex(Request $request)
    {
        $table = $request->get('table');
        $field = $request->get('field');

        $properties = DB::table($table)
            ->join('citynexus_properties', $table . '.property_id', '=', 'citynexus_properties.id')
            ->get();

        $results = [];
        foreach ($properties as $property)
        {

            $results[$property->$field][$property->id] = $property;
        }


        return view('citynexus::reports.pivots.index', compact('results', 'field', 'table'));
    }
    public function getPivotProfile(Request $request)
    {
        $table = $request->get('table');
        $field = $request->get('field');
        $owner = $request->get('owner');

        $pids = DB::table($table)
            ->where($field, $owner)
            ->lists('property_id');

        $properties = Property::find($pids);

        $pscores = [];

        $scores = Score::all();

        $result = null;
        foreach($pids as $i)
        {
            foreach($scores as $score)
            {
                if(Schema::hasTable('citynexus_scores_' . $score->id))
                {
                    $result = DB::table('citynexus_scores_' . $score->id)->where('property_id', $i)->get();
                }
                if($result != null)
                {
                    $pscores[$i][$score->id] = $result;
                }
            }

        }

        return view('citynexus::reports.pivots.profile', compact('properties', 'owner', 'pscore'));
    }

}