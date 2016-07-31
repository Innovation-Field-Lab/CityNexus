<form action="/{{config('citynexus.root_directory')}}/settings/update-user" class="form form-horizontal" method="post">
    {{csrf_field()}}
    <div class="form-group">
        <label for="email" class="control-label col-sm-4">Email Address</label>

        <div class="col-sm-8">
            <input type="text" class="form-control" id="email" name="email"
                   value="{{$user->email}}"/>
        </div>
    </div>
    <div class="form-group">
        <label for="password" class="control-label col-sm-4">Current Password</label>

        <div class="col-sm-8">
            <input type="password" class="form-control" id="password" name="password"/>
        </div>
    </div>
    <div class="form-group">
        <label for="new_password" class="control-label col-sm-4">New Password</label>

        <div class="col-sm-8">
            <input type="password" class="form-control" id="new_password" name="new_password"/>
        </div>
    </div>
    <div class="form-group">
        <label for="confirm_password" class="control-label col-sm-4">Confirm Password</label>

        <div class="col-sm-8">
            <input type="password" class="form-control" id="confirm_password" name="confirm_password"/>
        </div>
    </div>
    <div class="form-group">
        <label for="first_name" class="control-label col-sm-4">First Name</label>

        <div class="col-sm-8">
            <input type="text" class="form-control" id="first_name" name="first_name"
                   value="{{$user->first_name}}"/>
        </div>
    </div>
    <div class="form-group">
        <label for="last_name" class="control-label col-sm-4">Last Name</label>

        <div class="col-sm-8">
            <input type="text" class="form-control" id="last_name" name="confirm_password"
                   value="{{$user->last_name}}"/>
        </div>
    </div>
    <div class="form-group">
        <label for="title" class="control-label col-sm-4">Title</label>

        <div class="col-sm-8">
            <input type="text" class="form-control" id="title" name="title"
                   value="{{$user->title}}"/>
        </div>
    </div>
    <div class="form-group">
        <label for="department" class="control-label col-sm-4">Department</label>

        <div class="col-sm-8">
            <input type="text" class="form-control" id="department" name="department"
                   value="{{$user->department}}"/>
        </div>
    </div>
    <input type="submit" class="btn btn-primary" value="Update User">
</form>