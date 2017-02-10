<?php

namespace CityNexus\CityNexus\Http;

use App\Http\Controllers\Controller;
use App\User;
use Carbon\Carbon;
use CityNexus\CityNexus\Export;
use CityNexus\CityNexus\Note;
use CityNexus\CityNexus\Property;
use CityNexus\CityNexus\Score;
use CityNexus\CityNexus\Search;
use CityNexus\CityNexus\Table;
use CityNexus\CityNexus\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Queue;


class SearchController extends Controller
{

    public function getAdvancedSearch()
    {
        $scores = Score::orderBy('name')->get();
        $datasets = Table::whereNotNull('table_name')->orderBy('table_name')->get();
        $tags = Tag::select('tag')->lists('tag');
        return view('citynexus::search.advanced', compact('scores', 'datasets', 'tags'));
    }

    public function postAdvancedSearch(Request $request)
    {
        $filters = $request->get('filters');
        $pids = $this->applySearch($filters);
        $results = Property::find($pids);
        $tags = Tag::select('tag')->lists('tag');
        return view('citynexus::search.advanced_results', compact('results', 'filters', 'tags'));
    }

    public function postSaveSearch(Request $request)
    {
        switch ($request->get('type'))
        {
            case 'export':
                $this->saveExport($request);
                return redirect(action('\CityNexus\CityNexus\Http\ReportController@getExports'));
                break;

            case 'search':
                $this->saveSearch($request);
                return redirect(action('\CityNexus\CityNexus\Http\SearchController@getAllSearches'));
                break;

            case 'tags':
                $this->saveTags($request);
                return redirect(action('\CityNexus\CityNexus\Http\TagController@getIndex'));
                break;
        }
    }

    /**
     * @return string
     */
    public function getSavedSearches()
    {

    }


    private function saveExport($request)
    {
        $pid = $this->applySearch(json_decode($request->get('filters'), true));

        $properties = Property::find($pid);

        $results[0] =[
            'property_id',
            'full_address',
            'lat',
            'lng'
        ];

        foreach($properties as $property)
        {
            $results[$property->id] = [
                $property->id,
                $property->full_address,
                $property->lat,
                $property->long
            ];
        }

        $export = Export::create(['name' => $request->get("export"), 'elements' => ['_type' => 'saved_search']]);

        $name = str_replace(' ', '-', strtolower($export->name)) . '.csv';
        $path = storage_path() . '/export_cache/' . time() .'-' . $name;

        ksort($results);

        $fp = fopen($path, 'w');

        foreach($results as $row)
        {
            fputcsv($fp, $row);
        }

        fclose($fp);

        $export->source = $path;
        $export->save();
    }

    private function saveSearch($request)
    {
        $search = Search::create($request->all());

        return true;
    }

    private function saveTags($request)
    {
        $pids = $this->applySearch(\GuzzleHttp\json_decode($request->get('filters'), true));

        $update = [];
        foreach($request->get('tags') as $name)
        {
            $tag = Tag::firstOrCreate(['tag' => $name]);

            foreach($pids as $pid)
            {
                $update[] = [
                    'property_id' => $pid,
                    'tag_id' => $tag->id,
                    'created_by' => Auth::Id(),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ];
            }
        }

        DB::table('property_tag')->insert($update);
    }

    private function applySearch($filters)
    {
        $results = ['include' => [], 'exclude' => []];

        // if comment filter is on
        if(isset($filters['comments']['range']['on'])) {
            $results = $this->commentDateRange($results, $filters['comments']['range']['dates']);
        }

        $results = $this->commentText($results, $filters['comments']['text']);

        if(isset($filters['tags'])) $results = $this->searchTags($results, $filters['tags']);

        if(isset($filters['scores']))
            foreach ($filters['scores'] as $filter)
            {
                $results = $this->searchScores($results, $filter);
            }
        if(isset($filters['datasets']))
            foreach ($filters['datasets'] as $table => $filter)
            {
                $results = $this->searchDatasets($results, $table, current($filter));
            }

        $include = array_unique($results['include']);
        $exclude = array_unique($results['exclude']);

        $pids = array_diff($include, $exclude);

        return $pids;
    }

    private function commentText($results, $filter)
    {
        return $results;
    }

    private function commentDateRange($results, $filter)
    {
        $dates = explode(' - ', $filter);
        $comments = Note::whereDate('created_at', '>=', $dates[0])
        ->whereDate('created_at', '<=', $dates[1])
        ->whereNotNull('property_id')
        ->lists('property_id')->toArray();

        $results['include'] = array_merge($results['include'], $comments);

        return $results;
    }

    private function searchTags($results, $filters)
    {
        $include = [];
        if(isset($filters['include-current'])) {
            foreach($filters['include-current'] as $tag)
            {
                $id = Tag::where('tag', $tag)->pluck('id');
                $include = array_merge(DB::table('property_tag')->where('tag_id', $id)->lists('property_id'), $include);
            }
        }

        if(isset($filters['include-previous'])) {
            foreach ($filters['include-previous'] as $tag) {
                $id = Tag::where('tag', $tag)->whereNotNull('deleted_at')->pluck('id');
                $include = array_merge(DB::table('property_tag')->where('tag_id', $id)->lists('property_id'), $include);
            }
        }

        $exclude = [];
        if(isset($filters['exclude-current'])) {
            foreach ($filters['exclude-current'] as $tag) {
                $id = Tag::where('tag', $tag)->pluck('id');
                $exclude = array_merge(DB::table('property_tag')->where('tag_id', $id)->lists('property_id'), $exclude);
            }
        }

        if(isset($filters['exclude-previous'])) {
            foreach ($filters['exclude-previous'] as $tag) {
                $id = Tag::where('tag', $tag)->whereNotNull('deleted_at')->pluck('id');
                $exclude = array_merge(DB::table('property_tag')->where('tag_id', $id)->lists('property_id'), $exclude);
            }
        }

        $results['include'] = array_merge($results['include'], $include);
        $results['exclude'] = array_merge($results['exclude'], $exclude);

        return $results;
    }

    private function searchScores($results, $filter)
    {
        switch ($filter['type'])
        {
            case 'include':
                $results['include'] = array_merge(DB::table('citynexus_scores_' . $filter['id'])->lists('property_id'), $results['include']);
                break;
            case 'exclude';
                $results['exclude'] = array_merge(DB::table('citynexus_scores_' . $filter['id'])->lists('property_id'), $results['exclude']);
                break;
            case '>':
                $results['include'] = array_merge(DB::table('citynexus_scores_' . $filter['id'])->where('score', '>', $filter['test'])->lists('property_id'), $results['include']);
                break;
            case '<':
                $results['include'] = array_merge(DB::table('citynexus_scores_' . $filter['id'])->where('score', '<', $filter['test'])->lists('property_id'), $results['include']);
                break;
            case '=':
                $results['include'] = array_merge(DB::table('citynexus_scores_' . $filter['id'])->where('score', $filter['test'])->lists('property_id'), $results['include']);
                break;
        }

        return $results;
    }

    private function searchDatasets($results, $table, $filter)
    {
        switch ($filter['filter'])
        {
            case 'include':
                $results['exclude'] = array_merge(DB::table($table)->whereNotNull($filter['key'])->lists('property_id'), $results['exclude']);
                break;
            case 'exclude';
                $results['exclude'] = array_merge(DB::table($table)->whereNotNull($filter['key'])->lists('property_id'), $results['exclude']);
                break;
            case '>':
                $results['include'] = array_merge($this->searchDataset($table, $filter['method'], $filter), $results['include']);
                break;
            case '<':
                $results['include'] = array_merge($this->searchDataset($table, $filter['method'], $filter), $results['include']);
                break;
            case '==':
                $results['include'] = array_merge($this->searchDataset($table, $filter['method'], $filter), $results['include']);
                break;
            case '!=':
                $results['include'] = array_merge($this->searchDataset($table, $filter['method'], $filter), $results['include']);
                break;
            case 'notContains':
                $results['include'] = array_merge($this->searchDataset($table, $filter['method'], $filter), $results['include']);
                break;
            case 'contains':
                $results['include'] = array_merge($this->searchDataset($table, $filter['method'], $filter), $results['include']);
                break;
        }

        return $results;
    }

    private function searchDataset($table, $scope, $filter)
    {
        $field = $filter['key'];
        $query = "SELECT citynexus_properties.id, " . $table . ".created_at, " . $table . '.' . $field;
        $query .= ' FROM citynexus_properties ';
        $query .=  'INNER JOIN ' . $table . ' ON citynexus_properties.id = ' . $table . '.property_id ';
        $query .= 'ORDER BY ' . $table . '.created_at';
        $data = DB::select(DB::raw($query));

        $population = [];
        foreach($data as $i) $population[$i->id][] = $i;

        switch ($scope)
        {
            case 'sum':
                foreach($population as $key => $item)
                {
                    $results[$key] = array_sum($item);
                }
                break;
            case 'count':
                foreach($population as $key => $item)
                {
                    $results[$key] = count($item);
                }
                break;
            case 'mean':
                foreach($population as $key => $item)
                {
                    $results[$key] = array_sum($item)/count($item);
                }
                break;
            case 'most-recent':
                foreach($population as $key => $item)
                {
                    $results[$key] = last($item)->$field;
                }
                break;
            case 'any':
                foreach($population as $key => $item)
                {
                    $results[$key][] = last($item)->$field;
                }
                break;
        }

        $return = [];

        foreach($results as $pid => $values)
        {

            if(!is_array($values)) $values = [$values];

            $values = array_map('strtoupper', $values);

            switch ($filter['filter'])
            {
                case 'contains':
                    if (in_array(strtoupper($filter['test']), $values)) $return[] = $pid;
                    break;
                case 'notContains':
                    if (!in_array(strtoupper($filter['test']), $values)) $return[] = $pid;
                    break;
                case '==':
                    foreach($values as $value) if(strtolower($value) == strtolower($filter['test']))  $return[] = $pid;
                    break;
                case '!=':
                    foreach($values as $value) if(strtolower($value) != strtolower($filter['test']))  $return[] = $pid;
                    break;
            }
        }

        return $return;
    }

    public function getSearch(Request $request)
    {
        $query = "%" . $request->get('query') . "%";

        if(Property::where('full_address', 'LIKE', $query)->count() != 1)
        {

            // if ajax
            if($request->ajax())
            {
                return Property::where('full_address', 'LIKE', $query)->orderBy('full_address')->get();
            }
            else
            {
                $results = Property::where('full_address', 'LIKE', $query)->orderBy('full_address')->paginate(25);

                return view('citynexus::search.results', compact('results'));
            }
        }
        else
        {
            // if ajax
            if($request->ajax())
            {
                return Property::where('full_address', 'LIKE', $query)->first();
            }
            else
            {
                $property = Property::where('full_address', 'LIKE', $query)->first();
                return redirect(action('\CityNexus\CityNexus\Http\PropertyController@getShow', ['id' => $property->id]));
            }
        }
    }
    public function getQuery(Request $request)
    {
        $query = $request->get('query');
        $res   = Property::where('full_address', 'LIKE', "%$query%")->get();
        return $res;
        
    }

    public function getPrefetch()
    {
        $results = Property::lists('full_address');
        return $results;
    }
}