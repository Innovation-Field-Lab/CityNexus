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

class DatasetController extends Controller
{

    function __construct()
    {
        $this->dropbox = new Dropbox();
    }

    public function getTest()
    {
        $response = $this->dropbox->getFileList('Test');

        dd($response);
    }

    public function getDropboxSync($dataset_id)
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
        $settings = (object) $request->get('settings');
        $table_id = $request->get('dataset_id');
        $this->dropbox->processUpload($settings, $table_id, $request->get('download'));
        return view('citynexus::dataset.uploader.dropbox_success') ;
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


}
