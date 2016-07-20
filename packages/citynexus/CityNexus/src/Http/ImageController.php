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
        $image = $request->file('image');
        $imageFileName = time() . '.' . $image->getClientOriginalExtension();
        $s3 = Storage::disk('s3');
        $filePath = '/images/' . $imageFileName;
        $s3->put($filePath, file_get_contents($image), 'public');

        $img_object = new Image();
        $img_object->property_id = $request->get('property_id');
        $img_object->caption = $request->get('caption');
        $img_object->description = $request->get('description');
        $img_object->source = 'https://s3-us-west-2.amazonaws.com/' . env('S3_BUCKET') . '/' . $filePath;
        $img_object->save();

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
}