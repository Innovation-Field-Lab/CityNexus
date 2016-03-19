@extends(config('citynexus.template'))

@section(config('citynexus.section'))



            <form action="{{action('\CityNexus\CityNexus\Http\CitynexusSettingsController@postCreateUser')}}" class="form-horizontal" method="post">
                {{csrf_field()}}
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title" id="myModalLabel">Invite New User</h4>
                    </div>
                    <div class="panel-body">
                        @if (count($errors) > 0)
                            <div class="alert alert-danger">
                                <strong>Whoops!</strong> There were some problems with your input.<br><br>
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <div class="form-group">
                            <label for="first_name" class="control-label col-sm-4">First Name</label>

                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="first_name" name="first_name"
                                       value="{{old('first_name')}}" required/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="last_name" class="control-label col-sm-4">Last Name</label>

                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="last_name" name="last_name"
                                       value="{{old('last_name')}}" required/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="email" class="control-label col-sm-4">Email</label>

                            <div class="col-sm-8">
                                <input type="email" class="form-control" id="email" name="email" value="{{old('email')}}" required/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="admin" class="control-label col-sm-4">Admin User</label>

                            <div class="col-sm-8">
                                <input type="checkbox" id="admin" name="admin" value="true" @if(old('admin')) checked @endif"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="upload" class="control-label col-sm-4">Upload Permission</label>

                            <div class="col-sm-8">
                                <input type="checkbox" id="upload" name="upload"  value="true" @if(old('upload') != null) checked @endif/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="upload" class="control-label col-sm-4">Create Dataset Permission</label>

                            <div class="col-sm-8">
                                <input type="checkbox" id="dataset" name="dataset" value="true" @if(old('dataset') != null) checked @endif/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="upload" class="control-label col-sm-4">Create Scores Permission</label>

                            <div class="col-sm-8">
                                <input type="checkbox" id="scores" name="scores" value="true" @if(old('scores') != null) checked @endif/>
                            </div>
                        </div>
                    </div>
                    <div class="panel-footer">
                        <input type="submit" class="btn btn-primary" value="Invite user">
                    </div>
                </div>
            </form>
        </div>

    @stop