@extends(config('citynexus.template'))

@section(config('citynexus.section'))

    <div class="row">
        <div class="panel panel-default">
            <div class="panel-heading">
                Settings
            </div>
            <div class="panel-body">
                    <div class="bs-example bs-example-tabs" data-example-id="togglable-tabs">
                        <ul id="myTabs" class="nav nav-tabs" role="tablist">
                            <li role="presentation" class="active">
                                <a href="#user" id="user-tab" role="tab" data-toggle="tab" aria-controls="user" aria-expanded="true">
                                    User Settings
                                </a>
                            </li>
                            <li role="presentation" class="">
                                <a href="#dashboard_settings" id="dashboard_settings_tab" role="tab" data-toggle="tab" aria-controls="user" aria-expanded="false">
                                    Dashboard Settings
                                </a>
                            </li>
                            {{--<li role="presentation" class="">--}}
                                {{--<a href="#application" role="tab" id="application-tab" data-toggle="tab" aria-controls="application" aria-expanded="false">--}}
                                    {{--Application Settings--}}
                                {{--</a>--}}
                            {{--</li>--}}
                            @can('citynexus', ['usersAdmin', 'create'])
                            <li role="presentation" class="">
                                <a href="#users" role="tab" id="users-tab" data-toggle="tab" aria-controls="users" aria-expanded="false">
                                    User Accounts
                                </a>
                            </li>
                            @endcan
                            @can('citynexus', ['admin', 'edit'])
                            <li role="presentation" class="">
                                <a href="#app_settings" role="tab" id="app_settings-tab" data-toggle="tab" aria-controls="users" aria-expanded="false">
                                    CityNexus Settings
                                </a>
                            </li>
                            @endcan
                        </ul>
                        <div id="myTabContent" class="tab-content">
                            <div role="tabpanel" class="tab-pane fade active in" id="user" aria-labelledby="home-tab">
                                <div class="panel-body">
                                    @include(('citynexus::settings._user_settings'))
                                </div>
                            </div>
                            <div role="tabpanel" class="tab-pane fade" id="dashboard_settings" aria-labelledby="dashboard_settings-tab">
                                <div class="panel">
                                    <div class="panel-body">
                                        @include('citynexus::settings._dashboard_settings')
                                    </div>
                                </div>
                            </div>
                            <div role="tabpanel" class="tab-pane fade" id="application" aria-labelledby="profile-tab">
                                <div class="panel">
                                    <div class="panel-body">
                                        {{--<div class="form-group">--}}
                                            {{--<label for="gmaps_key" class="control-label col-sm-4">Google Maps API Key</label>--}}
                                            {{--<div class="col-sm-8">--}}
                                                {{--<input type="text" class="form-control" id="gmaps_key" name="gmaps_key"--}}
                                                       {{--value="@if(old('gmaps_key')){{old('gmaps_key')}}@elseif($app_s->where('key', 'gmaps_key')->count() > 0 ) {{$app_s->where(['key' => 'gmaps_key'])->first()}}@endif"/>--}}
                                            {{--</div>--}}
                                        {{--</div>--}}
                                        {{--<div class="form-group">--}}
                                            {{--<label for="gmaps_key" class="control-label col-sm-4">Google Maps API Key</label>--}}
                                            {{--<div class="col-sm-8">--}}
                                                {{--<input type="text" class="form-control" id="gmaps_key" name="gmaps_key"--}}
                                                       {{--value="@if(old('gmaps_key')){{old('gmaps_key')}}@elseif($app_s->where('key', 'gmaps_key')->count() > 0 ) {{$app_s->where(['key' => 'gmaps_key'])->first()}}@endif"/>--}}
                                            {{--</div>--}}
                                        {{--</div>--}}
                                    </div>
                                </div>
                            </div>

                            @can('citynexus', ['usersAdmin', 'create'])
                            <div role="tabpanel" class="tab-pane fade" id="users" aria-labelledby="users-tab">
                                <div class="panel">
                                    <div class="panel-body">
                                        @include('citynexus::settings._users')
                                    </div>
                                </div>
                            </div>
                            @endcan

                            @can('citynexus', ['admin', 'edit'])
                            <div role="tabpanel" class="tab-pane fade" id="app_settings" aria-labelledby="app_settings-tab">
                                <div class="panel">
                                    <div class="panel-body">
                                        @include('citynexus::settings._app_settings')
                                    </div>
                                </div>
                            </div>
                            @endcan
                        </div>
                    </div>
                </div>
        </div>
    </div>

@stop