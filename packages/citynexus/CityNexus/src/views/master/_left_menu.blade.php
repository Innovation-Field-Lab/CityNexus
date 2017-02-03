<!-- ========== Left Sidebar Start ========== -->
<div class="left side-menu">
    <div class="sidebar-inner slimscrollleft">

        <!-- User -->
        <div class="user-box">
            {{--<div class="user-img">--}}
            {{--<img src="/images/users/avatar-1.jpg" alt="user-img" title="Mat Helme" class="img-circle img-thumbnail img-responsive">--}}
            {{--<div class="user-status offline"><i class="zmdi zmdi-dot-circle"></i></div>--}}
            {{--</div>--}}
            <h5><a href="#">{{\Illuminate\Support\Facades\Auth::user()->fullname()}}</a> </h5>
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

                @can('citynexus', ['properties', 'view'])
                    <li class="has_sub">
                        <a href="javascript:void(0);" class="waves-effect @if(isset($section) && $section == 'properties') active @endif"><i class="fa fa-home"></i> <span> Properties</span> <span class="menu-arrow"></span></a>
                        <ul class="list-unstyled">
                            <li><a href="{{action('\CityNexus\CityNexus\Http\PropertyController@getIndex')}}">All Properties</a></li>
                            <li><a href="{{action('\CityNexus\CityNexus\Http\TagController@getIndex')}}"> All Tags</a></li>
                            @can('citynexus', ['properties', 'create'])<li><a href="{{action('\CityNexus\CityNexus\Http\PropertyController@getCreate')}}">Create New Property</a></li>@endcan
                        </ul>
                    </li>
                @endcan

                @can('citynexus', ['reports', 'view'])
                    <li class="has_sub">
                        <a href="javascript:void(0);" class="waves-effect @if(isset($section) && $section == 'reports') active @endif "><i class="fa fa-area-chart"></i> <span> Data Visualization </span> <span class="menu-arrow"></span></a>
                        <ul class="list-unstyled">
                            <li><a href="{{action('\CityNexus\CityNexus\Http\ViewController@getIndex')}}">Saved Views</a></li>
                            <li role="separator" class="divider"></li>
                            <li class="@if(isset($pagename) &&  $pagename == 'Dot Map') active @endif"><a href="{{action('\CityNexus\CityNexus\Http\ViewController@getDotMap')}}">Dot Map</a></li>
                            <li class="@if(isset($pagename) &&  $pagename == 'Scatter Chart') active @endif"><a href="{{action('\CityNexus\CityNexus\Http\ViewController@getScatterChart')}}">Scatter Chart Builder</a></li>
                            <li class="@if(isset($pagename) &&  $pagename == 'Bates Distribution') active @endif"><a href="{{action('\CityNexus\CityNexus\Http\ViewController@getDistribution')}}">Distribution Curve Builder</a></li>
                            <li class="@if(isset($pagename) &&  $pagename == 'Heat Map') active @endif"><a href="{{action('\CityNexus\CityNexus\Http\ViewController@getHeatMap')}}">Heat Map Builder</a></li>
                        </ul>
                    </li>
                @endcan

                @can('superAdmin')
                    <li class="has_sub">
                        <a href="javascript:void(0);" class="waves-effect @if(isset($section) && $section == 'reports') active @endif "><i class="fa fa-area-chart"></i> <span> Reports </span> <span class="menu-arrow"></span></a>
                        <ul class="list-unstyled">
                            <li class="@if(isset($pagename) &&  $pagename == 'Reports') active @endif"><a href="{{action('\CityNexus\CityNexus\Http\ReportController@getCreateProperty')}}">Create Property Report</a></li>
                        </ul>
                    </li>
                @endcan

                @can('citynexus', ['scores', 'view'])
                    <li class="has_sub">
                        <a href="javascript:void(0);" class="waves-effect @if(isset($section) && $section == 'scores') active @endif "><i class="fa fa-tachometer"></i> <span> Scores </span> <span class="menu-arrow"></span></a>
                        <ul class="list-unstyled">
                            <li><a href="{{action('\CityNexus\CityNexus\Http\RiskScoreController@getIndex')}}">All Scores</a></li>
                            @can('citynexus', ['group' => 'datasets', 'method' => 'create'])
                                <li role="separator" class="divider"></li>
                                <li><a href="{{action('\CityNexus\CityNexus\Http\RiskScoreController@getCreate')}}">Create New Score</a></li>
                            @endcan
                        </ul>
                    </li>
                @endcan

                @can('citynexus', ['datasets', 'view'])
                    <li class="has_sub">
                        <a href="javascript:void(0);" class="waves-effect @if(isset($section) && $section == 'datasets') active @endif "><i class="fa fa-database"></i> <span> Data Sets </span> <span class="menu-arrow"></span></a>
                        <ul class="list-unstyled">
                            <li><a href="{{action('\CityNexus\CityNexus\Http\TablerController@getIndex')}}">All Data Sets</a></li>
                            @can('citynexus', ['group' => 'datasets', 'method' => 'create'])
                                <li role="separator" class="divider"></li>
                                <li><a href="{{action('\CityNexus\CityNexus\Http\TablerController@getUploader')}}">New From Upload</a></li>
                                <li><a href="{{action('\CityNexus\CityNexus\Http\DatasetController@getDropboxSync')}}">New From Dropbox</a></li>
                            @endcan
                        </ul>
                    </li>
                @endcan
                @can('citynexus', ['export', 'view'])
                    <li class="has_sub">
                        <a href="javascript:void(0);" class="waves-effect @if(isset($section) && $section == 'exports') active @endif "><i class="fa fa-download"></i> <span> Export Reports </span> <span class="menu-arrow"></span></a>
                        <ul class="list-unstyled">
                            <li class="@if(isset($pagename) &&  $pagename == 'All Exports') active @endif"><a href="{{action('\CityNexus\CityNexus\Http\ReportController@getExports')}}">All Export Reports</a></li>
                            @can('citynexus', ['export', 'create'])<li class="@if(isset($pagename) &&  $pagename == 'Export Builder') active @endif"><a href="{{action('\CityNexus\CityNexus\Http\ReportController@getExportBuilder')}}">Create Export Report</a></li>@endcan
                        </ul>
                    </li>
                @endcan
                <li class="has_sub">
                    <a href="javascript:void(0);" class="waves-effect @if(isset($section) && $section == 'datasets') active @endif "><i class="fa fa-question-circle"></i> <span> Help </span> <span class="menu-arrow"></span></a>
                    <ul class="list-unstyled">
                        <li><a href="{{action('\CityNexus\CityNexus\Http\HelpController@getSubmitTicket')}}">Open Support Ticket</a></li>
                        <li><a href="https://citynexus.zendesk.com/hc/en-us" target="_blank">Help Center</a></li>

                    </ul>
                </li>

            </ul>
            <div class="clearfix"></div>
        </div>
        <!-- Sidebar -->
        <div class="clearfix"></div>

    </div>

</div>
<!-- Left Sidebar End -->