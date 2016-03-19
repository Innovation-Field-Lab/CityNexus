<div class="col-md-8">
    <table class="table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email Address</th>
                <th>Admin</th>
            </tr>
        </thead>
        @foreach($users as $user)
        <tr>
            <td>{{$user->first_name}} {{$user->last_name}}</td>
            <td>{{$user->email}}</td>
            <td>@if($user->admin) Admin @endif</td>
        </tr>
        @endforeach
    </table>
</div>

<div class="col-sm-4">
    <a href="{{action('\CityNexus\CityNexus\Http\CitynexusSettingsController@getCreateUser')}}" class="btn btn-primary pull-right" >Create New User</a>

</div>