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
}