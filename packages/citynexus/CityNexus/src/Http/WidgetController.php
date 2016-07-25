<?php

namespace CityNexus\CityNexus\Http;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use CityNexus\CityNexus\Property;
use CityNexus\CityNexus\DatasetQuery;
use CityNexus\CityNexus\GenerateScore;
use CityNexus\CityNexus\Score;
use CityNexus\CityNexus\Setting;
use CityNexus\CityNexus\Widget;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use CityNexus\CityNexus\Table;
use CityNexus\CityNexus\ScoreBuilder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Session;


class WidgetController extends Controller
{
    public function getCreate($type)
    {
        return view('citynexus::widgets.create.' . $type);
    }

    public function postCreate(Request $request)
    {
        $widget = $request->all();
        $widget['settings'] = json_encode($widget['settings']);
        $widget = Widget::create($widget);
        $return = '<li class="list-group-item" id="' . $widget->id . '"><i class="fa fa-sort"></i> ' . $widget->name. '</li>';
        return $return;
    }

    public function getRemove($id)
    {
        $widgets = setting('globalDashboard');
        foreach($widgets as $i)
        {
            if($id != $i->id)
            {
                $new[] = $i->id;
            }
        }
        $dashboard = Setting::where(['key' => 'globalDashboard']);
        $dashboard->value = json_encode($new);
        $dashboard->save();

        return 'success';

    }

    public function getDelete($id)
    {
        $widget = Widget::find($id);
        $widget->delete();

        return 'success';
    }

}