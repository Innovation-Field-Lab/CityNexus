<?php

namespace CityNexus\CityNexus\Http;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
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


class ImageController extends Controller
{
    public function postUpload(Request $request)
    {
        Image::create($request->all());
        return redirect()->back();
    }

    public function getDelete($id, Request $request)
    {
        Image::find($id)->delete();
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
        return Image::find($id);
    }

    public function getUploader(Request $request)
    {
        if(null != $request->get('property_id'))
        {
            $property_id = $request->get('property_id');
            return view('citynexus::image.uploader', compact('property_id'));
        }

        return view('citynexus::image.uploader');
    }
}