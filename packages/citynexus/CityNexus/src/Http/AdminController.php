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
        $tables = DB::table('information_schema.tables')->where('table_schema', 'public')->get();
        return view('citynexus::admin.index', compact('tables'));

    }

    public function getRefreshGeocoding()
    {
        $properties = Property::whereNull('lat')->get();

        foreach($properties as $i)
        {
            $this->dispatch(new GeocodeJob($i->id));
        }
    }

    public function getEditTable(Request $request)
    {
        $table_name = $request->get('table_name');

        return redirect('/tabler/show-table/' . $table_name);
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

    public function getRemoveChelsea()
    {

        DB::table('citynexus_properties')->where('city', 'chelsea')->update(['city' => env('CITY')]);
    }

    public function getMergeProperties()
    {
        $properties = Property::whereNull('alias_of')->orderBy('full_address')->lists('id', 'full_address');

        $sorted = array();

        foreach($properties as $k => $i)
        {
            $sorted[$k][] = $i;
        }

        dd($sorted);

        $counter = 0;
        foreach($sorted as $i)
        {
            if(count($i) > 1)
            {
                $p_id = $i[0];
                unset($i[0]);
                foreach($i as $a)
                {
                    Property::find($a->id)->update('alias_of', $p_id);
                    $counter++;
                }
            }

        }

        Session::flash('flash_success', $counter . " Properties updated!");

        return redirect()->back();

    }
}