<?php

namespace CityNexus\CityNexus\Http;

use CityNexus\CityNexus\Dropbox;
use CityNexus\CityNexus\Table;
use CityNexus\CityNexus\Upload;
use CityNexus\CityNexus\Uploader;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Mockery\CountValidator\Exception;
use CityNexus\CityNexus\Typer;
use CityNexus\CityNexus\TableBuilder;

class DatasetController extends Controller
{

    function __construct()
    {
        $this->dropbox = new Dropbox();
    }

    public function getDropboxSync($dataset_id = null)
    {
        return view('citynexus::dataset.dropbox_sync', compact('dataset_id'));
    }

    public function postCreateDropboxSync(Request $request)
    {
        $settings = (object) $request->get('settings');
        $items = $this->dropbox->getFileList($settings);
        if(is_object($items))
        {
            return view('citynexus::dataset.uploader.dropbox_chooser', compact('items'));
        }

    }

    public function postProcessDropboxSync(Request $request)
    {
        $table_id = $request->get('dataset_id');
        $path = $request->get('download');
        $settings = (object) $request->get('settings');

        if($table_id != null)
        {
            $this->dropbox->processUpload($settings, $table_id, $path);
            return view('citynexus::dataset.uploader.dropbox_success_json') ;
        }
        else
        {
            $table = $this->dropbox->download($settings->dropbox_token, $path);

            $table = Table::create(['raw_upload' => json_encode($table)]);
            $typer = new Typer();
            $builder = new TableBuilder();
            $table_id = $table->id;
            $table = \GuzzleHttp\json_decode($table->raw_upload)[0];
            $settings = json_encode($settings);
            return view('citynexus::dataset.api-scheme', compact('settings', 'path', 'table', 'typer', 'table_id', 'builder'));

        }
    }

    public function postScheduleDropbox(Request $request)
    {
        $uploader = $request->all();
        $uploader['settings_json'] = json_encode($uploader['settings']);
        $uploader['type'] = 'dropbox';
        $uploader = Uploader::create($uploader);

        Session::flash('flash_success', 'Dropbox Sync Scheduled');

        return redirect(action('\CityNexus\CityNexus\Http\TablerController@getIndex'));
    }

    public function postCreateScheme($id, Request $request)
    {
        $this->authorize('citynexus', ['datasets', 'create']);

        $this->validate($request, [
            'table_name' => 'max:255|required'
        ]);
        $tb = new TableBuilder();
        $tabler = new TablerController();

        $map = $request->get('map');
        $table = Table::find($id);
        $table->scheme = json_encode($map);
        $table->table_title = $request->get('table_name');
        $table->table_name = $tb->create($table);
        $table->description = $request->get('description');
        $table->settings = json_encode($request->get('settings'));
        $table->save();

        $upload = Upload::create(['table_id' => $table->id, 'note' => 'Dropbox Initial Upload']);

        $tabler->processUpload( $table, json_decode($table->raw_upload, true), $upload->id);
        $table->raw_upload = null;
        $table->save();
        $up_settings = \GuzzleHttp\json_decode($request->get('uploader_settings'));

        return view('citynexus::dataset.uploader.dropbox_success')
            ->with('dataset_id', $table->id)
            ->with('dropbox_path', $up_settings->dropbox_path)
            ->with('dropbox_token', $up_settings->dropbox_token);
    }

}
