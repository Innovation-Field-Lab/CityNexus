<div class="col-md-9">
    <table class="table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email Address</th>
                <th></th>
            </tr>
        </thead>
        @foreach($users as $user)
        <tr id="user-{{$user->id}}">
            <td>{{$user->first_name}} {{$user->last_name}}</td>
            <td>{{$user->email}}</td>
            <td>
                @if(\Illuminate\Support\Facades\Auth::getUser()->id != $user->id)
                    <button class="btn btn-xs btn-primary" onclick="editUserSettings({{$user->id}})">Settings</button>
                    <button class="btn btn-xs btn-danger" onclick="removeUser({{$user->id}})">Delete</button>
                    @can('super_user')
                        <button class="btn btn-xs btn-caution @if(!$user->super_admin) hidden @endif" id="desuper-{{$user->id}}" onclick="superUser(false, {{$user->id}})">Remove Super User</button>
                        <button class="btn btn-xs btn-default @if($user->super_admin) hidden @endif" id="super-{{$user->id}}" onclick="superUser(true, {{$user->id}})">Make Super User</button>

                    @endcan
                    @if($user->activation != null)
                        <a class="btn btn-xs btn-info" id="invite-{{$user->id}}" href="{{action('\CityNexus\CityNexus\Http\CitynexusSettingsController@getInvite')}}/{{$user->id}}">Re-Invite</a>
                    @endif
                @endif
            </td>
        </tr>
        @endforeach
    </table>
</div>

<div class="col-sm-3">
    <a href="{{action('\CityNexus\CityNexus\Http\CitynexusSettingsController@getCreateUser')}}" class="btn btn-primary pull-right" >Create New User</a>
</div>

<!-- Modal -->
<div class="modal fade" id="user-settings-modal" tabindex="-1" role="dialog" aria-labelledby="permissions">
    <form id="user-settings-form" action="/{{config('citynexus.root_directory')}}/settings/settings-update" method="post">

    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="permissions_title">User Settings</h4>
            </div>
            <div class="modal-body" id="user-settings">

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

    @if(\Illuminate\Support\Facades\Session::get('flash_token') != null)
    var newTitle = 'Invite User';
    var newBody = "<form action={{action('\CityNexus\CityNexus\Http\CitynexusController@postSendEmail')}} method='post' class='form'>" +
            '{{csrf_field()}}' +
            "<input type='hidden' name='to' value='{{\Illuminate\Support\Facades\Session::get('flash_email')}}'>" +
            "<input type='text' class='form-control' value='{{\Illuminate\Support\Facades\Session::get('flash_email')}}' disabled>" +
            "<br><input class='form-control' type='text' name='subject' value='Welcome to CityNexus'>'" +
            "<textarea class='form-control' name='message'>" +
                    "To activate your account follow this link: {{ url('/activate-account?key=' . \Illuminate\Support\Facades\Session::get('flash_token')) }}" +
            "</textarea>" +
            "<br><br><input type='submit' class='btn btn-primary' value='Invite User'>"

    triggerModal(newTitle, newBody);
    @endif

    function editUserSettings( id )
    {
        $.ajax({
            url: '/citynexus/settings/user-settings/' + id
        }).success( function( data ) {
            $('#user-settings').html(data);
            $('#user-settings-modal').modal('show');
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

    function removeUser( id )
    {
        $.ajax({
            url: '/{{config('citynexus.root_directory')}}/settings/remove-user',
            method: 'post',
            data: {
                _token: '{{csrf_token()}}',
                user_id: id
            }
        }).success(function(){
           $('#user-' + id).addClass('hidden');
        });
    }

    function superUser( status, id)
    {
        $.ajax({
            url: "/{{config('citynexus.root_directory')}}/settings/update-user-settings/" + id,
            method: 'post',
            data: {
                _token: '{{csrf_token()}}',
                user:{
                    super_admin: status
                }
            }
        }).success(function(){
           if(status)
           {
               $('#super-' + id).addClass('hidden');
               $('#desuper-' + id).removeClass('hidden');
           }
            else
           {
               $('#super-' + id).removeClass('hidden');
               $('#desuper-' + id).addClass('hidden');
           }
        });
    }
</script>

@endpush