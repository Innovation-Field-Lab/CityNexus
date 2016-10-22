<?php

namespace CityNexus\CityNexus\Http;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use CityNexus\CityNexus\File;
use CityNexus\CityNexus\FileVersion;
use CityNexus\CityNexus\Image;
use CityNexus\CityNexus\Property;
use CityNexus\CityNexus\DatasetQuery;
use CityNexus\CityNexus\GenerateScore;
use CityNexus\CityNexus\Score;
use CityNexus\CityNexus\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use CityNexus\CityNexus\Table;
use CityNexus\CityNexus\ScoreBuilder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;


class FileController extends Controller
{
    public function postUpload(Request $request)
    {
        $file = File::create($request->all());
        $version = FileVersion::create([
            'added_by'  => Auth::getUser()->id,
            'size'      => intval($request->get('size') / 1000),
            'type'      => $request->get('type'),
            'source'    => $request->get('source'),
            'file_id'   => $file->id
        ]);
        $file->version_id = $version->id;
        $file->save();

        return redirect()->back();
    }

    public function getDelete($id, Request $request)
    {
        File::find($id)->delete();
        if($request->isJson())
        {
            return response();
        }
        else
        {
            return redirect()->back();
        }
    }

    public function getShow($id)
    {
        return File::find($id)->getFile();
    }

    public function getDownload($id)
    {
        return redirect(File::find($id)->current->source);
    }

    public function getUploader(Request $request)
    {
        if(null != $request->get('property_id'))
        {
            $property_id = $request->get('property_id');
            return view('citynexus::file.uploader', compact('property_id'));
        }

        return view('citynexus::file.uploader');
    }
}