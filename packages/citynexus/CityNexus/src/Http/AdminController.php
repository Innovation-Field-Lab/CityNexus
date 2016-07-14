<?php

namespace CityNexus\CityNexus\Http;

use App\User;
use Carbon\Carbon;
use CityNexus\CityNexus\CreateRaw;
use CityNexus\CityNexus\GeocodeJob;
use CityNexus\CityNexus\Location;
use CityNexus\CityNexus\MergeProps;
use CityNexus\CityNexus\ProcessData;
use CityNexus\CityNexus\Property;
use CityNexus\CityNexus\Upload;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
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
        $this->authorize('citynexus', ['admin', 'view']);
        $tables = DB::table('information_schema.tables')->where('table_schema', 'public')->get();
        return view('citynexus::admin.index', compact('tables'));

    }

    public function getProcessData($id, $table_name)
    {
        $this->dispatch(new ProcessData($id, $table_name));

        return redirect()->back();
    }

    public function getRefreshGeocoding()
    {
        $this->authorize('citynexus', ['admin', 'view']);

        $properties = Property::whereNull('lat')->get();

        foreach ($properties as $i) {
            $this->dispatch(new GeocodeJob($i->id));
        }
    }

    public function getEditTable(Request $request)
    {
        $this->authorize('citynexus', ['admin', 'view']);

        $table_name = $request->get('table_name');

        return redirect('/tabler/show-table/' . $table_name);
    }

    public function getRemoveData(Request $request)
    {
        $this->authorize('citynexus', ['admin', 'delete']);

        DB::table($request->get('table_name'))->where('id', $request->get('row_id'))->delete();

        Session::flash('flash_info', "Row successfully remove");

        return redirect()->back();

    }

    public function getClearTable($table_name, $remove = false)
    {
        $this->authorize('citynexus', ['admin', 'delete']);

        if ($remove) {
            Schema::drop($table_name);
            Session::flash('flash_info', "Table Removed");
        } else {
            DB::table($table_name)->truncate();
            Session::flash('flash_info', "Table Cleared");
        }

        return redirect('/');
    }

    protected function schedule(Schedule $schedule)
    {
        $date = Carbon::now()->toW3cString();
        $environment = env('APP_ENV');
        $schedule->command(
            "db:backup --database=mysql --destination=s3 --destinationPath=/{$environment}/projectname_{$environment}_{$date} --compression=gzip"
        )->weekly();
    }

    public function getMigrateAdmin()
    {
        $users = User::all();
        foreach($users as $i)
        {
            if($i->admin)
            {
                $i->super_admin = true;
                $i->save();
            }
        }

        Session::flash('flash_success', 'Updates completed');
        return redirect()->back();
    }


    public function getCreateRawRows($table_name)
    {
        if(!Schema::hasColumn($table_name, 'raw'))
        {
            Schema::table($table_name, function(Blueprint $table)
            {
                $table->json('raw')->nullable();
            });
        }

        $all = DB::table($table_name)->count();
        $data = DB::table($table_name)->whereNull('raw')->get();
        $edited = 0;
        foreach($data as $i)
        {
            DB::table($table_name)->where('id', $i->id)->update(['raw' => json_encode($i)]);
            $edited++;
        }

        $message = $edited . ' out of ' . $all . ' records edited';
        if(DB::table('tabler_tables')->where('table_name', $table_name)->first()->settings != null)  $settings = \GuzzleHttp\json_decode(DB::table('tabler_tables')->where('table_name', $table_name)->pluck('settings'), true);
        $settings['raw_migrated'] = true;

        DB::table('tabler_tables')->where('table_name', $table_name)->update(['settings' => json_encode($settings)]);

        Session::flash('flash_success', $message);

        return redirect()->back();
    }


    public function getSaveRawAddress($table = null, $id = null)
    {
        if($id != null)
        {
            $tableBuilder = new TableBuilder();
            $tableBuilder->saveRawAddress($table, $id);
            return 'success';
        }

        else
        {
            if($table == null)
            {
                $tables = Table::whereNotNull('table_name')->get();

                foreach($tables as $i)
                {
                    $dataset = DB::table($i->table_name)->whereNotNull('property_id')->get(['id']);
                    $dataset = array_chunk($dataset, 200);
                    foreach($dataset as $ids)
                    {
                        $this->dispatch(new CreateRaw($i->table_name, $ids));
                    }
                }

            }

            else{
                $dataset = DB::table($table)->whereNotNull('property_id')->get(['id']);
                $dataset = array_chunk($dataset, 200);
                foreach($dataset as $ids)
                {
                    $this->dispatch(new CreateRaw($table, $ids));
                }
            }


        }
        Session::flash('flash_success', 'That worked!');
        return redirect('/');
    }



}