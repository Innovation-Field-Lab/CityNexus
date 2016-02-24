<?php

namespace CityNexus\CityNexus\Http;

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
        return view('citynexus::index', compact('tables'));
    }
    public function getUploader()
    {
        return view('citynexus::uploader');
    }

    public function postUploader(Request $request)
    {
        $this->validate($request, [
                'file' => 'required'
            ]);
        $table = Excel::load($request->file('file'), function($reader){$reader->toArray();});

        $table = Table::create(['raw_upload' => json_encode(end($table))]);

        return redirect(action('\CityNexus\CityNexus\Http\TablerController@getCreateScheme', ['table_id' => $table->id]));
    }

    public function getCreateScheme(Request $request)
    {

        $table = json_decode(Table::find($request->get('table_id'))->raw_upload);
        $typer = new Typer();
        $table = end($table);
        $table_id = $request->get('table_id');

        return view('citynexus::create-scheme', compact('table', 'typer', 'table_id'));
    }

    /**
     * @param Request $request
     */
    public function postCreateScheme(Request $request)
    {
        $tabler = new TableBuilder();


        $table = Table::find($request->get('table_id'));
        $table->scheme = json_encode($request->get('map'));
        $table->table_title = $request->get('table_name');
        $table->table_name = $tabler->create($table);
        $table->table_description = $request->get('table_description');

        $this->processUpload( $table, json_decode($table->raw_upload, true));

        $table->raw_upload = null;
        $table->save();


        return redirect('/tabler/');
    }

    public function getNewUpload(Request $request)
    {
        $table = Table::find($request->get('table_id'));
        return view('tabler::new-upload', compact('table'));
    }


    public function postNewUpload(Request $request)
    {
        $table = Table::find($request->get('table_id'));

        //get uploaded file
        $file = $request->file('file');

        //turn file into an object
        $data = Excel::load($file, function($reader){$reader->toArray();});


        $data = json_encode(end($data));

        //process upload

        $this->processUpload( $table, json_decode($data, true));

        Session::flash('flash_success', 'Data successfully uploaded!');

        return redirect('/tabler/');
    }

    public function getEditTable(Request $request)
    {
        $table = Table::find($request->get('table_id'));
        $scheme = json_decode($table->scheme);
        return view('tabler::edit', compact('table', 'scheme'));
    }

    public function postUpdateTable(Request $request)
    {
        $this->validate($request, [
            'table_title' => 'max:255|required',
            'table_description' => 'required',
            'map' => 'required'
        ]);

        try {

            $table = Table::find($request->get('id'));

            $table->table_title = $request->get('table_title');
            $table->table_description = $request->get('table_description');
            $table->scheme = json_encode($request->get('map'));

            $table->save();
        }
        catch(Exception $e)
        {
            Session::flash('flash_danger', '$e');

            return back();
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
     * @return bool
     */

    public function processUpload($table, $data)
    {
        $scheme = json_decode($table->scheme);

        $tabler = new TableBuilder();

        //create an array of sync values
        $syncValues = $tabler->findValues( $table->scheme, 'sync' );

        $pushValues = $tabler->findValues( $table->scheme, 'push' );

        $data = array_chunk($data, 500);

        try
        {
            foreach($data as $i)
            {
                $this->dispatch(new UploadData($i, $table, $scheme, $syncValues, $pushValues));
            }
        }
        catch(Exception $e)
        {
            Session::flash('flash_warning', 'Uh oh. ' . $e);

            return redirect()->back();
        }

        Session::flash('flash_success', "Upload has been successfully queued.");

        // TODO: Send alert to uploading user that process is complete

        return true;
    }
}