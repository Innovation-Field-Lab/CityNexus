<form action="{{action('\CityNexus\CityNexus\Http\DatasetController@postScheduleDropbox')}}" method="post">
    {!! csrf_field() !!}
    <h4>Upload Success</h4>

    <p>Your file has successfully uploaded!</p>

    <p>Please select how frequently you would like this folder connection to be regularly checked.</p>

    <div class="form-group">
        <label for="frequency" class="control-label col-sm-4">Freqency</label>

        <div class="col-sm-8">
            <select type="text" class="form-control" id="frequency" name="frequency">
                <option value="">Select One</option>
                <option value="hourly">Hourly</option>
                <option value="daily">Daily</option>
                <option value="weekly">Weekly</option>
                <option value="monthly">Monthly</option>
            </select>
        </div>
    </div>

    <div class="form-group">
        <label for="" class="control-label col-sm-4"></label>

        <div class="col-sm-8">
            <button class="form-control btn btn-primary">Schedule Dropbox Sync</button>
        </div>
    </div>
    <input type="hidden" name="settings[dropbox_token]" id="final_settings_dropbox_token">
    <input type="hidden" name="settings[dropbox_path]" id="final_settings_dropbox_path">

    <input type="hidden" name="dataset_id" id="final_dataset_id">
</form>