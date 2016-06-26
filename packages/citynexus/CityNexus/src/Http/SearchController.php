<?php

namespace CityNexus\CityNexus\Http;

use App\Http\Controllers\Controller;
use App\User;
use CityNexus\CityNexus\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Queue;


class SearchController extends Controller
{
    public function getSearch(Request $request)
    {
        $property = Property::where('full_address', 'LIKE', $request->get('query'))->first();

        return redirect(action('\CityNexus\CityNexus\Http\PropertyController@getShow', ['id' => $property->id]));
    }
    public function getQuery(Request $request)
    {
        $query = $request->get('query');
        $res   = Property::where('full_address', 'LIKE', "%$query%")->get();
        return $res;
        
    }

    public function getPrefetch()
    {
        $results = Property::lists('full_address');
        return $results;
    }
}