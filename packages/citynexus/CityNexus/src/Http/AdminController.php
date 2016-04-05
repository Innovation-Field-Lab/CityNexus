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
        $properties = Property::all();

        foreach($properties as $i)
        {
            $this->dispatch(new GeocodeJob($i->id));
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

    public function getRemoveData(Request $request)
    {
        DB::table($request->get('table_name'))->where('id', $request->get('row_id'))->delete();

        Session::flash('flash_info', "Row successfully remove");

        return redirect()->back();

    }

    public function getClearTable($table_name, $remove = false)
    {
        if($remove)
        {
            Schema::table($table_name)->drop();
            Session::flash('flash_info', "Table Removed");
        }
        else
        {
            DB::table($table_name)->truncate();
            Session::flash('flash_info', "Table Cleared");
        }

        return redirect()->back();
    }
}