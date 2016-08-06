<div class="form-group">
    <label for="settings[dropbox_app]" class="control-label col-sm-4">Dropbox App</label>

    <div class="col-sm-8">
        <input type="text" class="form-control" id="settings_dropbox_app" name="settings[dropbox_app]" value="{{old('settings[dropbox_app]')}}"/>
    </div>

</div>
<div class="form-group">
    <label for="settings[dropbox_secret]" class="control-label col-sm-4">Dropbox Secret</label>

    <div class="col-sm-8">
        <input type="text" class="form-control" id="settings_dropbox_secret" name="settings[dropbox_secret]"
               value="{{old('settings[dropbox_secret]')}}"/>
    </div>
</div>
<div class="form-group">
    <label for="settings[dropbox_token]" class="control-label col-sm-4">Dropbox Token</label>

    <div class="col-sm-8">
        <input type="text" class="form-control" id="settings_dropbox_token" name="settings[dropbox_token]" value="{{old('settings[dropbox_token]')}}"/>
    </div>
</div>

<div class="form-group">
    <label for="settings[path]" class="control-label col-sm-4">Dropbox Path</label>

    <div class="col-sm-8">
        <input type="text" class="form-control" id="settings[path]" name="settings[path]"
               value="{{old('settings[path]')}}"/>
    </div>
</div>

<input type="submit" value="Test Connection" class="btn btn-primary">