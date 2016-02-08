@if(\Illuminate\Support\Facades\Session::get('flash_success') != null)
    @include('includes.alerts._success', ['alert' => \Illuminate\Support\Facades\Session::get('flash_success')])

@elseif(\Illuminate\Support\Facades\Session::get('flash_info') != null)
    @include('includes.alerts._info', ['alert' => \Illuminate\Support\Facades\Session::get('flash_info')])

@elseif(\Illuminate\Support\Facades\Session::get('flash_warning') != null)
    @include('includes.alerts._warning', ['alert' => \Illuminate\Support\Facades\Session::get('flash_warning')])

@elseif(\Illuminate\Support\Facades\Session::get('flash_danger') != null)
    @include('includes.alerts._danger', ['alert' => \Illuminate\Support\Facades\Session::get('flash_danger')])

@endif