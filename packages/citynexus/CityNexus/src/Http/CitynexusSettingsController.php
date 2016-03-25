<?php

namespace CityNexus\CityNexus\Http;

use App\Http\Controllers\Controller;
use App\User;
use CityNexus\CityNexus\Property;
use CityNexus\CityNexus\DatasetQuery;
use CityNexus\CityNexus\GenerateScore;
use CityNexus\CityNexus\Score;
use CityNexus\CityNexus\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use CityNexus\CityNexus\Table;
use CityNexus\CityNexus\ScoreBuilder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Session;
use League\Flysystem\Exception;
use CityNexus\CityNexus\InviteUser;


class CitynexusSettingsController extends Controller
{
    public function getIndex()
    {
        $app_s = Setting::all();
        $user_s = Setting::where('user_id', Auth::id());
        $users = User::all();

        return view('citynexus::settings.edit', compact('app_s', 'user_s', 'users'));

    }

    public function getCreateUser()
    {
        return view('citynexus::settings.createUser');
    }

    public function postCreateUser(Request $request)
    {
        $this->validate($request, [
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'email' => 'required|max:255|email',
        ]);

        try {
            // Save the request as a new User
            $user = new User();

            $user->first_name = $request->get('first_name');
            $user->last_name = $request->get('last_name');
            $user->email = $request->get('email');
            $user->password = str_random();
            $user->admin = $this->testPref($request->all(), 'admin');

            //Create the permissions object
            $user->permissions = $this->createPermissionObject($request->all());

            //Save the user Model
            $user->save();

            $this->dispatch(new InviteUser($user->id));

        }
        catch(\Exception $e) {
            Session::flash('flash_warning', "Uh oh. " . $e);
            return redirect()->back()->withInput();
        }
        finally
        {
        }
        Session::flash('flash_success', "New user successfully invited.");

        return redirect(action('\CityNexus\CityNexus\Http\CitynexusSettingsController@getIndex'));
    }

    private function createPermissionObject($request)
    {
        $return = array();
        $return['upload'] = $this->testPref($request, 'upload');
        $return['scores'] = $this->testPref($request, 'scores');
        $return['dataset'] = $this->testPref($request, 'dataset');

        return json_encode($return);
    }

    private function testPref($request, $pref)
    {
        if(isset($request[$pref]) | isset($request['admin'])) return true; else return false;
    }
}