<?php


namespace CityNexus\CityNexus;

use CityNexus\CityNexus\Http\TablerController;
use Maatwebsite\Excel\Facades\Excel;

class Dropbox
{

    /**
     *
     * Generate list of contents of a folder
     *
     * @param $settings
     * @return object
     */
    public function getFileList($settings)
    {
        $data =[
            'path' => $settings->dropbox_path
        ];
        $post = json_encode($data);

        $url = 'https://api.dropboxapi.com/2/files/list_folder';
        $curl = curl_init($url); //initialise
        curl_setopt($curl,CURLOPT_HTTPHEADER,array('Authorization: Bearer ' . $settings->dropbox_token,'Content-Type: application/json'));
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);

        $items = \GuzzleHttp\json_decode($response)->entries;

        $items = (object) $items;

        return $items;
    }


    /**
     *
     * Create a new dataset from a dropbox upload
     *
     * @param $settings array
     * @param $table_id int
     * @param null $path boolean
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function processUpload($settings, $table_id, $path = null)
    {
        //Open Dropbox Connection
        if(!$path)
        {
            $list = $this->getFileList($settings);
            $last_file = end($list);
            $path = $last_file->id;
        }

        $data = $this->download($settings->dropbox_token, $path);
        //Get Table

        $table = Table::find($table_id);
        $table->touch();
        $upload = Upload::create(['table_id' => $table_id, 'note' => 'Dropbox upload']);
        $tabler = new TablerController();
        $tabler->processUpload($table, $data, $upload->id);

        return view('citynexus::dataset.uploader.dropbox_success');
    }

    /**
     *
     * Get metadata of a dropbox file
     *
     * @param $token string
     * @param $path string
     * @return array|mixed
     */
    public function getMetadata($token, $path)
    {
        $data =[
            'path' => $path
        ];
        $post = json_encode($data);

        $url = 'https://api.dropboxapi.com/2/files/get_metadata';
        $curl = curl_init($url); //initialise
        curl_setopt($curl,CURLOPT_HTTPHEADER,array('Authorization: Bearer ' . $token,'Content-Type: application/json'));
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);

        $data = \GuzzleHttp\json_decode($response);

        return $data;
    }

    /**
     *
     * Download a CSV or Excel file from dropbox and return contents in array
     *
     * @param $token
     * @param $path
     * @return array
     */
    public function download($token, $path)
    {
        $url = 'https://content.dropboxapi.com/2/files/download';
        $curl = curl_init($url); //initialise

        $data =[
            'path' => $path
        ];
        $post = json_encode($data);
        curl_setopt($curl,CURLOPT_HTTPHEADER,array('Authorization: Bearer ' . $token,'Content-Type: ', 'Dropbox-API-Arg: ' . $post));
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);
        $file = $response;

        $metadata = $this->getMetadata($token, $path);

        file_put_contents(storage_path($metadata->name), $file);

        //process and delete temp file
        $data = Excel::load(storage_path($metadata->name), function($reader){$reader->toArray();})->parsed;

        unlink(storage_path($metadata->name));

        return $data;


    }
}