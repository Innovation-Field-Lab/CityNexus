<?php
namespace CityNexus\CityNexus\Http;
use App\Http\Controllers\Controller;
use CityNexus\CityNexus\Export;
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
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\Datatables\Facades\Datatables;

class ReportController extends Controller
{
    public function getCreateProperty()
    {
        $this->authorize('citynexus', ['reports', 'create']);
        $datasets = Table::whereNotNull('table_name')->get();
        $tables = new Table();
        return view('citynexus::reports.property.create', compact('datasets', 'tables'));
    }

    public function getExportBuilder()
    {
        $datasets = Table::whereNotNull('table_name')->orderBy('table_title')->get();
        $scores = Score::all();
        return view('citynexus::reports.export.builder', compact('datasets', 'scores'));
    }

    public function postExportSave(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:255'
        ]);

        $export = Export::create($request->all());

        return redirect(action('\CityNexus\CityNexus\Http\ReportController@getRefreshExport', $export->id));
    }

    public function getExports()
    {
        $exports = Export::orderBy('updated_at', 'DESC')->paginate(15);
        return view('citynexus::reports.export.index', compact('exports'));

    }

    public function getRefreshExport($id)
    {
        $export = Export::find($id);
        $this->buildExport($export);
        return redirect(action('\CityNexus\CityNexus\Http\ReportController@getExports'));

    }

    public function getDownloadExport($id)
    {
        $export = Export::find($id);
        return response()->download($export->source);
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
                    $return["geocoordinates"] = 'Lat: ' . $location->lat. ', Lng: ' . $property->long;
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

    private function buildExport($export)
    {

        $random = '_' . str_random(8);

        $name = str_replace(' ', '-', strtolower($export->name)) . '.csv';
        $path = storage_path() . '/export_cache/' . time() .'-' . $name;

        $elements = $export->elements;
        $results = [];
        if(isset($elements['datasets'])) $results = $this->exportDatasets($elements['datasets'], $results);
        if(isset($elements['scores'])) $results = $this->exportScores($elements['scores'], $results);

        $properties = Property::find(array_keys($results));

        $plist = [];

        $plist[0]['property_id'] = 'property_id';
        if(isset($export->elements['property']['full_address']))
        {
            $plist[0]['full_address'] = 'full_address';
        }

        if(isset($export->elements['property']['parsed']))
        {
            $plist[0]['house_number'] = 'house_number';
            $plist[0]['street_name'] = 'street_name';
            $plist[0]['street_type'] = 'street_type';
            $plist[0]['unit'] = 'unit';
            $plist[0]['city'] = 'city';
            $plist[0]['state'] = 'state';
            $plist[0]['zip'] = 'zip';
        }

        if(isset($export->elements['property']['coordinates'])) {
            $plist[0]['lat'] = 'lat';
            $plist[0]['long'] = 'long';
        }

        foreach ($properties as $property)
        {
            $plist[$property->id]['property_id'] = $property->id;

            if(isset($export->elements['property']['full_address']))
            {
                $plist[$property->id]['full_address'] = $property->full_address;
            }

            if(isset($export->elements['property']['parsed']))
            {
                $plist[$property->id]['house_number'] = $property->house_number;
                $plist[$property->id]['street_name'] = $property->street_name;
                $plist[$property->id]['street_type'] = $property->house_number;
                $plist[$property->id]['unit'] = $property->unit;
                $plist[$property->id]['city'] = $property->city;
                $plist[$property->id]['state'] = $property->state;
                $plist[$property->id]['zip'] = $property->zip;
            }

            if(isset($export->elements['property']['coordinates']))
            {
                $plist[$property->id]['lat'] = $property->lat;
                $plist[$property->id]['long'] = $property->long;
            }

        }

        foreach($results as $pid => $result)
        {
            if(isset($plist[$pid]))
            {
                $results[$pid] = array_merge($plist[$pid], $results[$pid]);
            }
            else
            {
                $results[$pid] = array_merge((array) Property::where('id', $pid)->pluck('id', 'full_address', 'lat', 'long'), $results[$pid]);
            }

        }

        ksort($results);

        $fp = fopen($path, 'w');

        foreach($results as $row)
        {
            fputcsv($fp, $row);
        }

        fclose($fp);

        $export->source = $path;
        $export->save();

        return 'success';
    }

    public function exportDatasets($settings, $results)
    {
        $data = [];

        foreach($settings as $key => $elements)
        {
            $query = "SELECT citynexus_properties.id, " . $key . ".created_at, ";

            // create list of fields to be selected
            $fields = [];
            foreach($elements as $element)
            {
                $fields[] = $key . '.' . $element['key'];
            }

            //clear duplicates
            $fields = array_unique($fields);

            $i = 0;
            $len = count($fields);

            foreach($fields as $field)
            {
                $query .= $field;
                if ($i != $len - 1) $query .= ', ';
                $i++;
            }

            $query .= ' FROM citynexus_properties ';

            $query .=  'INNER JOIN ' . $key . ' ON citynexus_properties.id = ' . $key . '.property_id ';

            $query .= 'ORDER BY ' . $key . '.created_at';

            $data[$key] = DB::select(DB::raw($query));

        }

        $proceesed = [];

        foreach($data as $key => $item)
        {
            foreach($item as $set => $dataset)
            {
                foreach($dataset as $k => $i)
                {
                    $proceesed[$dataset->id][$key][$set][$k] = $i;
                }
            }
        }

        foreach($settings as $table => $element)
        {
            foreach($element as $i)
            {
                switch ($i['method'])
                {
                    case 'most-recent':
                        $results = $this->processMostRecent($table, $i['key'], $proceesed, $results);
                        break;
                    case 'all':
                        $results = $this->processAll($table, $i['key'], $proceesed, $results);
                        break;
                    case 'mean':
                        $results = $this->processMean($table, $i['key'], $proceesed, $results);
                        break;
                    case 'count':
                        $results = $this->processCount($table, $i['key'], $proceesed, $results);
                        break;
                    case 'sum':
                        $results = $this->processSum($table, $i['key'], $proceesed, $results);
                        break;
                }
            }
        }
        return $results;
    }

    private function exportScores($scores, $results)
    {

        foreach($scores as $score)
        {
            $score_model = Score::find($score);
            $data = DB::table('citynexus_scores_' . $score)->get(['property_id', 'score']);
            $name = 'score_' . str_replace(' ', '_', strtolower($score_model->name));

            $results[0][$name] = $name;

            foreach ($data as $i)
            {
                $results[$i->property_id][$name] = $i->score;
            }
        }

        return $results;
    }

    private function processMostRecent($table, $key, $data, $results)
    {
        foreach($data as $pid => $datasets)
        {
            // Add name to titles
            $results[0][$table . '_' . $key] = $table . '_' . $key;

            if(isset($datasets[$table]))
            {
                $results[$pid][$table . '_' . $key] = current($datasets[$table])[$key];
            }
        }


        return $results;
    }


    private function processAll($table, $key, $data, $results)
    {

        foreach($data as $pid => $datasets)
        {
            // Add name to titles
            $results[0][$table . '_' . $key . '_all'] = $table . '_' . $key . '_all';

            if(isset($datasets[$table]))
            {
                foreach($datasets[$table] as $item)
                {
                    if(isset($results[$pid][$table . '_' . $key . '_all']))
                    {
                        $results[$pid][$table . '_' . $key . '_all'] = $results[$pid][$table . '_' . $key . '_all'] . ', ' . $item[$key];
                    }
                    else
                    {
                        $results[$pid][$table . '_' . $key . '_all'] = $item[$key];
                    }
                }
            }
        }

        return $results;
    }

    private function processMean($table, $key, $data, $results)
    {
        foreach($data as $pid => $datasets)
        {
            // Add name to titles
            $results[0][$table . '_' . $key . '_mean'] = $table . '_' . $key . '_mean';

            if(isset($datasets[$table]))
            {
                $sample = [];
                foreach($datasets[$table] as $item)
                {
                    if($item[$key] != null)
                    {
                        $sample[] = $item[$key];
                    }
                }
                if(count($sample) > 0)
                {
                    $results[$pid][$table . '_' . $key . '_mean'] = array_sum($sample) / count($sample);
                }
                else
                {
                    $results[$pid][$table . '_' . $key . '_mean'] = null;
                }
            }
        }

        return $results;
    }

    private function processSum($table, $key, $data, $results)
    {
        foreach($data as $pid => $datasets)
        {
            // Add name to titles
            $results[0][$table . '_' . $key . '_sum'] = $table . '_' . $key . '_sum';

            if(isset($datasets[$table]))
            {
                $sample = [];
                foreach($datasets[$table] as $item)
                {
                    if($item[$key] !== null)
                    {
                        $sample[] = $item[$key];
                    }
                }
                $results[$pid][$table . '_' . $key . '_sum'] = array_sum($sample);
            }
        }

        return $results;
    }

    private function processCount($table, $key, $data, $results)
    {
        foreach($data as $pid => $datasets)
        {
            // Add name to titles
            $results[0][$table . '_' . $key . '_count'] = $table . '_' . $key . '_count';

            if(isset($datasets[$table]))
            {
                $sample = [];
                foreach($datasets[$table] as $item)
                {
                    if($item[$key] !== null)
                    {
                        $sample[] = $item[$key];
                    }
                }
                $results[$pid][$table . '_' . $key . '_count'] = count($sample);
            }
        }

        return $results;
    }

    public function postDeleteExport(Request $request)
    {
        $export = Export::find($request->get('export_id'));
        if(file_exists($export->source)) unlink($export->source);
        $export->delete();

        return response('Success');
    }

}