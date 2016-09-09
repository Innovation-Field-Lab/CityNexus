<?php

namespace CityNexus\CityNexus\Http;

use App\Http\Controllers\Controller;
use App\User;
use CityNexus\CityNexus\GeocodeJob;
use CityNexus\CityNexus\Property;
use CityNexus\CityNexus\DatasetQuery;
use CityNexus\CityNexus\GenerateScore;
use CityNexus\CityNexus\Score;
use CityNexus\CityNexus\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use CityNexus\CityNexus\Table;
use CityNexus\CityNexus\ScoreBuilder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Queue;
use CityNexus\CityNexus\Geocode;
use CityNexus\CityNexus\Tag;
use Illuminate\Support\Facades\Session;
use Pheanstalk\Command\ReserveCommand;


class TagController extends Controller
{

    public function getIndex()
    {
        $tags = Tag::with('properties')->get();
        return view('citynexus::tags.index', compact('tags'));
    }

    public function getPinMap($id)
    {
        $tag = Tag::find($id);
        $pins = $tag->properties()->select('lat', 'long', 'id', 'full_address')->get();


        return view('citynexus::reports.maps.pinmap', compact('pins', 'tag'));
    }

    public function getList($id)
    {
        $tag = Tag::find($id);
        return view('citynexus::tags.list', compact('tag'));
    }

    public function postRename(Request $request)
    {
        $tag = Tag::find($request->get('tag_id'));
        $tag->tag = $request->get('name');
        $tag->save();
        Session::flash('flash_success', 'Tag successfully renamed.');
        return redirect(action('\CityNexus\CityNexus\Http\TagController@getIndex'));
    }

    public function postMergeTags(Request $request)
    {
        DB::table('property_tag')->where('tag_id', $request->get('old_id'))->update(['tag_id' => $request->get('new_id')]);
        Tag::find($request->get('old_id'))->delete();
        Session::flash('flash_success', 'Tags successfully merged.');
        return redirect(action('\CityNexus\CityNexus\Http\TagController@getIndex'));
    }

    public function getDelete($id)
    {
        $tags = DB::table('property_tag')->where('tag_id', $id)->delete();
        Tag::find($id)->delete();

        return redirect()->back();
    }


}