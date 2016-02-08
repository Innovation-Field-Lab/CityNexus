<?php

namespace App\Http\Controllers;

use App\ApiKey;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Salaback\Tabler\Table;

class APIController extends Controller
{
    public function postAddTo(Request $request)
    {

        $app = ApiKey::find($request->get('app_id'));



        if($request->get('key') == $app->key)
        {
            $table = Table::find($request->get('dataset_id'));

            $data = json_decode($request->get('data'), true);

            $now = Carbon::now();

            foreach($data as $i)
            {
                // Add timestamps
                $i['updated_at'] = $now;

                //Check if a created at time stamp is being passed
                if(!isset($i['created_at'])) $i['created_at'] = $now;


                DB::table($table->table_name)
                    ->insert($i);
            }

            return 'success';
        }

        else
        {
            return response("Permission denied", 505);
        }
    }
}
