<div class="col-sm-8">
    <div class="horizontal-form">
        <div class="form-group">
            <label for="google_api_key" class="control-label col-sm-4">Google API Key</label>

            <div class="col-sm-8">
                <input onchange="saveSetting('google_api_key')" type="text" class="form-control" id="google_api_key" name="google_api_key"
                       value="{{setting('google_api_key')}}" />
            </div>
        </div>
    </div>
</div>
<div class="col-sm-4">
    <div class="card-box">
        <b>Visible Account Modules</b>
        <div class="list-group">
            <div class="list-group-item">
               <div class="btn btn-xs btn-success @if(setting('charts_and_maps_visible') == 'false' or setting('charts_and_maps_visible') == null) hidden @endif" id="charts_and_maps_visible_true" onclick="toggleSetting('false', 'charts_and_maps_visible')">Turn On</div> <div class="btn btn-xs btn-danger @if(setting('charts_and_maps_visible') == 'true') hidden @endif" id="charts_and_maps_visible_false" onclick="toggleSetting('true', 'charts_and_maps_visible')">Turn On</div> <b>Charts and Maps</b>
            </div>
        </div>
    </div>
</div>

@push('js_footer')

<script>

    function saveSetting( key )
    {
        var value = $('#' + key).val();
        postSetting( key, value);
    }
    function toggleSetting(state, key){
        postSetting (key, state);
        $('#' + key + '_' + state).removeClass('hidden');
        if(state == 'true')
            $('#' + key + '_false').addClass('hidden');
        if(state == 'false')
            $('#' + key + '_true').addClass('hidden');
    }
    function postSetting( key, value )
    {
        $.ajax({
            url: "{{action('\CityNexus\CityNexus\Http\CitynexusSettingsController@postSaveSetting')}}",
            type: 'POST',
            data: {
                _token: '{{csrf_token()}}',
                key: key,
                value: value
            }
        }).success(function(){
            Command: toastr["success"](name, "Setting has been saved!");
        }).error(function( data ){
            console.log(data.message)
            Command: toastr["warning"](name, "Something went wrong and a value for " + " has not been saved. Check console log.");

        })
    }
</script>

@endpush