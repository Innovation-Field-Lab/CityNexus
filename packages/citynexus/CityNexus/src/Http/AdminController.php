<?php

namespace CityNexus\CityNexus\Http;

use App\Jobs\FakeLocation;
use App\User;
use Carbon\Carbon;
use CityNexus\CityNexus\CreateRaw;
use CityNexus\CityNexus\Error;
use CityNexus\CityNexus\GeocodeJob;
use CityNexus\CityNexus\Location;
use CityNexus\CityNexus\MergeProps;
use CityNexus\CityNexus\ProcessData;
use CityNexus\CityNexus\Property;
use CityNexus\CityNexus\Upload;
use Geocoder\Geocoder;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
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

    public function getProcessData($table_name, $id = false, $without_pid = false)
    {
        if($id == 'all_records')
        {
            $ids = DB::table($table_name)->lists(['id']);
            $ids = array_chunk($ids, 500);
            foreach($ids as $i)
            {
                if($without_pid)
                {
                    $this->dispatch(new ProcessData($i->id, $table_name, true));
                }
                else
                {
                    $this->dispatch(new ProcessData($i->id, $table_name));
                }

            }
        }
        elseif($id != null)
        {
            $this->dispatch(new ProcessData($id, $table_name));
        }

        else
        {
            $ids = DB::table($table_name)->whereNull('property_id')->get(['id']);

            foreach($ids as $i)
            {
                if($without_pid)
                {
                    $this->dispatch(new ProcessData($i, $table_name, true));
                }
                else
                {
                    $this->dispatch(new ProcessData($i, $table_name));
                }
            }

        }

        Session::flash('flash_success', 'Data processing queued.');

        return redirect()->back();
    }

    public function getRefreshGeocoding()
    {
        $this->authorize('citynexus', ['admin', 'view']);

        $locactions = Location::whereNull('lat')->get();

        foreach ($locactions as $i) {
            $this->dispatch(new GeocodeJob($i->id));
        }
    }

    public function getEditTable(Request $request)
    {
        $this->authorize('citynexus', ['admin', 'view']);

        $table_name = $request->get('table_name');

        return redirect('/tabler/show-table/' . $table_name);
    }

    public function getFakeLocations()
    {
        $properties = Property::get(['id'])->chunk(100);
        foreach($properties as $i)
        {
            $this->dispatch(new FakeLocation($i));
        }
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

                    $dataset = DB::table($i->table_name)->whereNotNull('property_id')->lists('id');
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

            Session::flash('flash_success', 'That worked!');

        }

        return redirect('/');
    }

    public function getCreateFullAddresses()
    {
        $properties = Property::whereNull('full_address')->get();
        $count = 0;
        foreach($properties as $i)
        {
            $i->full_address = trim($i->house_number . ' ' . $i->street_name . ' ' . $i->street_type . ' ' . $i->unit);
            $i->save();
            $count++;

            return $count;
        }
    }

    public function getGeocodeErrors()
    {
        $count = 0;
        $errors = Error::where('location', 'geocode')->get();
        foreach($errors as $i)
        {
            $property = Property::find(\GuzzleHttp\json_decode($i->data)->property_id);

            try{
                $location = Location::firstOrCreate(['full_address' => $property->full_address]);
                if(env('APP_ENV') != 'testing')
                {
                    $geocode = Geocoder::geocode(   $location->full_address  . ', ' . config('citynexus.city_state'));
                    $location->lat = $geocode->getLatitude();
                    $location->long = $geocode->getLongitude();
                    $location->polygon = \GuzzleHttp\json_encode($geocode->getBounds());
                    $location->street_number = $geocode->getStreetNumber();
                    $location->street_name = $geocode->getStreetName();
                    $location->locality = $geocode->getCity();
                    $location->postal_code = $geocode->getZipcode();
                    $location->sub_locality = $geocode->getRegion();
                    $location->country = $geocode->getCountry();
                    $location->country_code = $geocode->getCountryCode();
                    $location->timezone = $geocode->getTimezone();
                }
                $location->save();
                $property->location_id = $location->id;
                $property->save();

                $count++;
            }
            catch(\Exception $e)
            {
                Error::create(['location' => 'geocode', 'data' => \GuzzleHttp\json_encode(['property_id' => $property->id])]);
            }
            $i->delete();
        }
        return $count;
    }

    public function getMergeAliases()
    {

        $properties = Property::all()->lists('alias_of');

        foreach($properties as $i)
        {
            if($i != null)
            {
                $array[$i] = $i;
            }
        }

        $properties = array_chunk($array, 500);

        foreach($properties as $i)
        {
            $this->dispatch(new MergeProps($i));
        }
    }

    public function getDropZeroAddresses()
    {

        $properties = Property::where('house_number', 0)->get();
        $datasets = DB::table('tabler_tables')->whereNotNull('table_name')->lists('table_name');

        foreach ($properties as $i) {
            DB::table('citynexus_images')->where('property_id', $i->id)->update(['property_id' => null]);
            DB::table('citynexus_notes')->where('property_id', $i->id)->update(['property_id' => null]);
            DB::table('citynexus_properties')->where('alias_of', $i->id)->update(['alias_of' => null]);
            DB::table('citynexus_raw_addresses')->where('property_id', $i->id)->update(['property_id' => null]);
            DB::table('citynexus_taskables')->where('citynexus_taskable_id', $i->id)->where('citynexus_taskable_type', 'CityNexus\CityNexus\Property')->delete();
            DB::table('property_tag')->where('property_id', $i->id)->delete();

            foreach ($datasets as $tn) {
                if (Schema::hasTable($tn)) {
                    DB::table($tn)->where('property_id', $i->id)->update(['property_id' => null]);
                }
            }

            $i->delete();
        }

        return 'success';
    }

    public function getDropNoData()
    {

        $properties = Property::where('house_number', 0)->get();
        $datasets = DB::table('tabler_tables')->whereNotNull('table_name')->lists('table_name');

            $images = DB::table('citynexus_images')->lists('property_id');
            $notes = DB::table('citynexus_notes')->lists('property_id');
            $properties = DB::table('citynexus_properties')->lists('alias_of');
            $raw = DB::table('citynexus_raw_addresses')->lists('property_id');
            $tasks = DB::table('citynexus_taskables')->where('citynexus_taskable_type', 'CityNexus\CityNexus\Property')->lists('citynexus_taskable_id');
            $tag = DB::table('property_tag')->lists('property_id');

            $ids = array_merge($images, $notes, $properties, $raw, $tasks, $tag);

            $data_ids = [];
            foreach ($datasets as $tn) {
                if (Schema::hasTable($tn)) {
                    $new_ids = DB::table($tn)->lists('property_id');
                    $data_ids = array_merge($new_ids, $data_ids);
                }
            }

            $ids = array_merge($ids, $data_ids);
            $ids = array_filter($ids);

            $ap = Property::all()->lists('id');

            $no_data = [];
            foreach($ap as $i)
            {
                if(array_search($i, $ids) == null)
                {
                    Property::find($i)->delete();
                }
            }

            return $no_data;

    }

    public function getClearNullDataset()
    {
        $datasets = Table::where('table_title', NULL)->withTrashed()->get();

        foreach($datasets as $i)
        {
            $i->forceDelete();
        }

        return "that worked";
    }

}