<?php

namespace CityNexus\CityNexus\Http;

use CityNexus\CityNexus\GeocodeJob;
use CityNexus\CityNexus\Property;
use CityNexus\CityNexus\Upload;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;
use Mockery\CountValidator\Exception;
use CityNexus\CityNexus\Typer;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use CityNexus\CityNexus\Table;
use CityNexus\CityNexus\UploadData;
use CityNexus\CityNexus\TableBuilder;

class AdminController extends Controller
{

    public function getIndex()
    {
        $tables = DB::table('information_schema.tables')->where('table_schema', 'public')->get();
        return view('citynexus::admin.index', compact('tables'));

    }

    public function getRefreshGeocoding()
    {
        $properties = Property::where('lat', null)->lists('id');

        foreach($properties as $i)
        {
            $this->dispatch(new GeocodeJob($i));
        }
    }

    public function getEditTable(Request $request)
    {
        $table_name = $request->get('table_name');
        $table = DB::table($request->get('table_name'))->get();
        $tables = DB::table('information_schema.tables')->where('table_schema', 'public')->get();


        $table = $table;

        return view('citynexus::admin.edit-table', compact('table', 'table_name', 'tables'));
    }

}