@extends(config('citynexus.template'))

@section(config('citynexus.section'))

    <div class="row">
        <div class="panel panel-default">
            <div class="panel-heading">
                Settings
            </div>
            <form action="" class="form-horizontal">
            <div class="panel-body">
                    <div class="bs-example bs-example-tabs" data-example-id="togglable-tabs">
                        <ul id="myTabs" class="nav nav-tabs" role="tablist">
                            <li role="presentation" class="active">
                                <a href="#user" id="user-tab" role="tab" data-toggle="tab" aria-controls="user" aria-expanded="true">
                                    User Settings
                                </a>
                            </li>
                            <li role="presentation" class="">
                                <a href="#application" role="tab" id="application-tab" data-toggle="tab" aria-controls="application" aria-expanded="false">
                                    Application Settings
                                </a>
                            </li>
                        </ul>
                        <div id="myTabContent" class="tab-content">
                            <div role="tabpanel" class="tab-pane fade active in" id="user" aria-labelledby="home-tab">
                                <div class="row">
                                    <div class="form-group">

                                    </div>
                                </div>
                            </div>
                            <div role="tabpanel" class="tab-pane fade" id="application" aria-labelledby="profile-tab">
                                <div class="panel">
                                    <div class="panel-body">
                                        <div class="form-group">
                                            <label for="gmaps_key" class="control-label col-sm-4">Google Maps API Key</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control" id="gmaps_key" name="gmaps_key"
                                                       value="@if(old('gmaps_key')){{old('gmaps_key')}}@elseif($app_s->where('key', 'gmaps_key')->count() > 0 ) {{$app_s->where(['key' => 'gmaps_key'])->first()}}@endif"/>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="gmaps_key" class="control-label col-sm-4">Google Maps API Key</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control" id="gmaps_key" name="gmaps_key"
                                                       value="@if(old('gmaps_key')){{old('gmaps_key')}}@elseif($app_s->where('key', 'gmaps_key')->count() > 0 ) {{$app_s->where(['key' => 'gmaps_key'])->first()}}@endif"/>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel-footer">
                    <input type="submit" class="btn btn-primary" value="Update Settings">
                </div>
            </form>
        </div>
    </div>

@stop