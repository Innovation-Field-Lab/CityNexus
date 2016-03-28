<?php

namespace CityNexus\CityNexus\Http;

use CityNexus\CityNexus\Property;
use CityNexus\CityNexus\Upload;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Mockery\CountValidator\Exception;
use CityNexus\CityNexus\Typer;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use CityNexus\CityNexus\Table;
use CityNexus\CityNexus\UploadData;
use CityNexus\CityNexus\TableBuilder;

class TablerController extends Controller
{

    public function getIndex()
    {
        $tables = Table::all();
        return view('citynexus::tabler.index', compact('tables'));
    }
    public function getUploader()
    {
        return view('citynexus::tabler.uploader');
    }

    public function postUploader(Request $request)
    {
        $this->validate($request, [
                'file' => 'required'
            ]);

        $table = Excel::load($request->file('file'), function($reader){$reader->toArray();});

        $table = Table::create(['raw_upload' => json_encode($table)]);

        return redirect(action('\CityNexus\CityNexus\Http\TablerController@getCreateScheme', ['table_id' => $table->id]));
    }

    public function getCreateScheme($id)
    {

        $table = json_decode(Table::find($id)->raw_upload)->parsed;
        $typer = new Typer();
        $builder = new TableBuilder();
        $table = end($table);
        $table_id = $id;

        return view('citynexus::tabler.create-scheme', compact('table', 'typer', 'table_id', 'builder'));
    }

    /**
     * @param Request $request
     */
    public function postCreateScheme($id, Request $request)
    {
        $this->validate($request, [
           'table_name' => 'max:255|required'
        ]);
        $tabler = new TableBuilder();

        $map = $request->get('map');
        $table = Table::find($id);
        $table->scheme = json_encode($map);
        $table->table_title = $request->get('table_name');
        $table->table_name = $tabler->create($table);
        $table->table_description = $request->get('table_description');
        $table->save();

        $upload = Upload::create(['table_id' => $table->id, 'note' => 'Initial Upload']);

        $this->processUpload( $table, json_decode($table->raw_upload, true)['parsed'], $upload->id);

        $table->raw_upload = null;
        $table->save();

        return redirect('/tabler/');
    }

    public function getNewUpload($id)
    {
        $table = Table::find($id);
        return view('citynexus::tabler.new-upload', compact('table'));
    }


    public function postNewUpload($id, Request $request)
    {

        $this->validate($request, [
           'note' => 'max:255'
        ]);

        $table = Table::find($id);


        //get uploaded file
        $file = $request->file('file');

        //turn file into an object
        $data = Excel::load($file, function($reader){$reader->toArray();});

        $data = json_encode($data);

        $upload = Upload::create([
            'table_id' => $id,
        ]);

        if($request->get('note') != null)
        {
            $upload->note = $request->get('note')->save();
        }

        $this->processUpload( $table, json_decode($data, true)['parsed'], $upload->id);

        Session::flash('flash_success', 'Data successfully uploaded and is being processed!');

        $table->touch();

        return redirect('/tabler/');
    }

    public function getEditTable($id)
    {
        $table = Table::find($id);
        $scheme = json_decode($table->scheme);
        return view('citynexus::tabler.edit', compact('table', 'scheme'));
    }

    public function postUpdateTable($id, Request $request)
    {
        $this->validate($request, [
            'table_title' => 'max:255|required',
            'map' => 'required'
        ]);

        try {

            $table = Table::find($id);

            $table->table_title = $request->get('table_title');
            $table->table_description = $request->get('table_description');
            $table->scheme = json_encode($request->get('map'));

            $table->save();
        }
        catch(Exception $e)
        {
            Session::flash('flash_warning', $e);

            return $e;
        }
        finally
        {
            Session::flash('flash_success', 'Dataset changes saved successfully.');
        }

        return redirect('/' . config('tabler.root_directory'));
    }


    /**
     *
     * Puts the contents of an array of data into a known table
     *
     * @param $table
     * @param $data
     * @param $upload_id
     * @return bool
     */

    public function processUpload($table, $data, $upload_id)
    {

        $data = array_chunk($data, 5);

        try
        {
            foreach($data as $i)
            {
                $this->dispatch(new UploadData($i, $table->id, $upload_id));
            }
        }
        catch(Exception $e)
        {
            Session::flash('flash_warning', 'Uh oh. ' . $e);

            return redirect()->back();
        }

//        Artisan::call('queue:listen');

        Session::flash('flash_success', "Upload has been successfully queued.");

        // TODO: Send alert to uploading user that process is complete

        return true;
    }

    public function getMergeRecords($id)
    {
        $property = Property::find($id);
        return view('citynexus::property.merge', compact('property'));
    }

    public function postMergeSearch(Request $request)
    {
        $search = '%' . strtolower($request->get('search')) . '%';
        $results = Property::where('full_address', 'LIKE', $search)->get(['id', 'full_address']);
        $id = $request->get('id');

        return view('citynexus::property.merge_results', compact('results', 'id'));
    }

    public function postMergeRecords(Request $request)
    {
        $aliases = $request->alias;
        $id = $request->get('p_id');
        foreach($aliases as $i)
        {
            $i = Property::find($i);
            $i->alias_of = $id;
            $i->save();
        }

        Session::flash('flash_success', "Records have been recorded as aliases.");
        return redirect(action('\CityNexus\CityNexus\Http\CitynexusController@getProperty', ['property_id' => $id]));
    }

    public function getRemoveTable($id)
    {
        $table = Table::find($id);
        $table_title = $table->table_title;
        $table->delete();
        Session::flash('flash_info', 'Table "' .  $table_title . '" successfully deleted');
        return redirect()->back();
    }

    public function getRollback($id)
    {
        $table = Table::find($id);
        return view('citynexus::tabler.rollback', compact('table'));
    }
    public function getRemoveUpload($id)
    {
        $upload = Upload::find($id);
        DB::table($upload->table->table_name)->where('upload_id', $upload->id)->delete();

        $upload->delete();

        return redirect(config('citynexus.tabler_root'));
    }
}