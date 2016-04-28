<div class="col-md-8">
    <table class="table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email Address</th>
                <th>Admin</th>
                <th></th>
            </tr>
        </thead>
        @foreach($users as $user)
        <tr>
            <td>{{$user->first_name}} {{$user->last_name}}</td>
            <td>{{$user->email}}</td>
            <td>@if($user->admin) Admin @endif</td>
            <td><button class="btn btn-xs btn-primary" onclick="editPermissions({{$user->id}})">Permissions</button></td>
        </tr>
        @endforeach
    </table>
</div>

<div class="col-sm-4">
    <a href="{{action('\CityNexus\CityNexus\Http\CitynexusSettingsController@getCreateUser')}}" class="btn btn-primary pull-right" >Create New User</a>
</div>

<!-- Modal -->

<div class="modal fade" id="permissions" tabindex="-1" role="dialog" aria-labelledby="permissions">
    <form id="permissions" action="/{{config('citynexus.root_directory')}}/settings/permissions" method="post">

    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="permissions_title">Permissions</h4>
            </div>
            <div class="modal-body" id="permissions-modal">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <input type="submit" class="btn btn-primary" value="Save Permissions" />
            </div>
        </div>
    </div>
    </form>
</div>

@push('js_footer')
<script>
    function editPermissions( id )
    {
        $.ajax({
            url: '/citynexus/settings/permissions/' + id
        }).success( function( data ) {
            $('#permissions-modal').html(data);
            $('#permissions').modal('show');
        })
    }

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

    function savePermissions()
    {
        var form =  $('#permissions');

        console.log( form );

        $.ajax({
            url: "/{{config('citynexus.root_directory')}}/settings/permissions",
            type: 'post',
            data: {
                _token: '{{csrf_token()}}',
                form: form.serializeArray()
            }
        }).success(function()
        {
//            $('#permissions').modal('hide');
        })

    }
</script>

@endpush