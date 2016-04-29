<!-- ========== Left Sidebar Start ========== -->
<div class="left side-menu">
    <div class="sidebar-inner slimscrollleft">

        <!-- User -->
        <div class="user-box">
            {{--<div class="user-img">--}}
                {{--<img src="/images/users/avatar-1.jpg" alt="user-img" title="Mat Helme" class="img-circle img-thumbnail img-responsive">--}}
                {{--<div class="user-status offline"><i class="zmdi zmdi-dot-circle"></i></div>--}}
            {{--</div>--}}
            <h5><a href="#">{{\Illuminate\Support\Facades\Auth::getUser()->fullname()}}</a> </h5>
            <ul class="list-inline">
                <li>
                    <a href="{{action('\CityNexus\CityNexus\Http\CitynexusSettingsController@getIndex')}}" >
                        <i class="zmdi zmdi-settings"></i>
                    </a>
                </li>

                <li>
                    <a href="/auth/logout" class="text-custom">
                        <i class="zmdi zmdi-power"></i>
                    </a>
                </li>
            </ul>
        </div>
        <!-- End User -->

        <!--- Sidemenu -->
        <div id="sidebar-menu">
            <ul>
                <li class="text-muted menu-title">Navigation</li>

                <li>
                    <a href="/" class="waves-effect @if(isset($section) && $section == 'dashboard') active @endif"><i class="zmdi zmdi-view-dashboard"></i> <span> Dashboard </span> </a>
                </li>

                @can('properties', 'view')
                <li class="has_sub">
                    <a href="javascript:void(0);" class="waves-effect @if(isset($section) && $section == 'properties') active @endif"><i class="fa fa-home"></i> <span> Properties</span> <span class="menu-arrow"></span></a>
                    <ul class="list-unstyled">
                        <li><a href="{{action('\CityNexus\CityNexus\Http\CitynexusController@getProperties')}}">All Properties</a></li>
                        <li><a href="{{action('\CityNexus\CityNexus\Http\TagController@getIndex')}}"> All Tags</a></li>
                    </ul>
                </li>
                @endcan

                @can('datasets', 'view')
                <li class="has_sub">
                    <a href="javascript:void(0);" class="waves-effect @if(isset($section) && $section == 'datasets') active @endif "><i class="fa fa-database"></i> <span> Data Sets </span> <span class="menu-arrow"></span></a>
                    <ul class="list-unstyled">
                        <li><a href="{{action('\CityNexus\CityNexus\Http\TablerController@getIndex')}}">All Data Sets</a></li>
                        @can('datasets', 'create')
                        <li role="separator" class="divider"></li>
                        <li><a href="{{action('\CityNexus\CityNexus\Http\TablerController@getUploader')}}">New From Upload</a></li>
                        @endcan
                    </ul>
                </li>
                @endcan

                @can('scores', 'view')
                <li class="has_sub">
                    <a href="javascript:void(0);" class="waves-effect @if(isset($section) && $section == 'scores') active @endif "><i class="fa fa-area-chart"></i> <span> Scores </span> <span class="menu-arrow"></span></a>
                    <ul class="list-unstyled">
                        <li><a href="{{action('\CityNexus\CityNexus\Http\RiskScoreController@getIndex')}}">All Scores</a></li>
                        @can('datasets', 'create')
                        <li role="separator" class="divider"></li>
                        <li><a href="{{action('\CityNexus\CityNexus\Http\RiskScoreController@getCreate')}}">Create New Score</a></li>
                        @endcan
                    </ul>
                </li>
                @endcan

            </ul>
            <div class="clearfix"></div>
        </div>
        <!-- Sidebar -->
        <div class="clearfix"></div>

    </div>

</div>
<!-- Left Sidebar End -->