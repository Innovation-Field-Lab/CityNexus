<?php

namespace CityNexus\CityNexus\Http;

use CityNexus\CityNexus\GeocodeJob;
use CityNexus\CityNexus\MergeProps;
use CityNexus\CityNexus\Property;
use CityNexus\CityNexus\Upload;
use Illuminate\Database\Schema\Blueprint;
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
        $this->authorize('admin', 'view');
        $tables = DB::table('information_schema.tables')->where('table_schema', 'public')->get();
        return view('citynexus::admin.index', compact('tables'));

    }

    public function getRefreshGeocoding()
    {
        $this->authorize('admin', 'view');

        $properties = Property::whereNull('lat')->get();

        foreach($properties as $i)
        {
            $this->dispatch(new GeocodeJob($i->id));
        }
    }

    public function getEditTable(Request $request)
    {
        $this->authorize('admin', 'view');

        $table_name = $request->get('table_name');

        return redirect('/tabler/show-table/' . $table_name);
    }

    public function getRemoveData(Request $request)
    {
        $this->authorize('admin', 'delete');

        DB::table($request->get('table_name'))->where('id', $request->get('row_id'))->delete();

        Session::flash('flash_info', "Row successfully remove");

        return redirect()->back();

    }

    public function getClearTable($table_name, $remove = false)
    {
        $this->authorize('admin', 'delete');

        if($remove)
        {
            Schema::drop($table_name);
            Session::flash('flash_info', "Table Removed");
        }
        else
        {
            DB::table($table_name)->truncate();
            Session::flash('flash_info', "Table Cleared");
        }

        return redirect('/');
    }

    public function getMergeProperties()
    {
        $this->authorize('admin', 'view');

        $properties = Property::all();

        $sorted = array();
        $counter = 0;
        foreach($properties as $i)
        {
            if(isset($sorted[trim($i->full_address)]))
            {
                Property::find($i->id)->update(['alias_of' => $sorted[trim($i->full_address)]]);
                $counter++;
            }
            else
            {
                $sorted[trim($i->full_address)] = $i->id;
            }
        }

        Session::flash('flash_success', $counter . " Properties updated!");

        return redirect()->back();

    }
}