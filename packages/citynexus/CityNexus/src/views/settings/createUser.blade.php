@extends(config('citynexus.template'))

@section(config('citynexus.section'))

            @include('citynexus::master._errors')

            <form action="{{action('\CityNexus\CityNexus\Http\CitynexusSettingsController@postCreateUser')}}" class="form-horizontal" method="post">
                {{csrf_field()}}
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title" id="myModalLabel">Invite New User</h4>
                    </div>
                    <div class="panel-body">
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
                            <label for="title" class="control-label col-sm-4">Title</label>

                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="title" name="title" value="{{old('title')}}"/>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="department" class="control-label col-sm-4">Department</label>

                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="department" name="department " value="{{old('department')}}"/>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="admin" class="control-label col-sm-4">Super Admin User</label>

                            <div class="col-sm-8">
                                <input type="checkbox" id="admin" name="super_admin" value="true" @if(old('admin')) checked @endif"/>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="admin" class="control-label col-sm-4">Permissions</label>

                            <div class="col-sm-8">
                            @include('citynexus::settings._permissions')
                            </div>
                        </div>


                    </div>
                    <div class="panel-footer">
                        <input type="submit" class="btn btn-primary" value="Invite User">
                    </div>
                </div>
            </form>
        </div>

    @stop

    @push('js_footer')

    <script>
        function select( type  )
        {
            event.preventDefault();
            $('.' + type).prop("checked", true);
            $('#' + type + 'SelectAll').addClass('hidden');
            $('#' + type + 'UnselectAll').removeClass('hidden');
        }

        function clearChecks( type  )
        {
            event.preventDefault()
            $('.' + type).prop("checked", false);
            $('#' + type + 'SelectAll').removeClass('hidden');
            $('#' + type + 'UnselectAll').addClass('hidden');
        }
    </script>

    @endpush