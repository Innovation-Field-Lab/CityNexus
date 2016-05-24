<!-- Right Sidebar -->
<div class="side-bar right-bar">
    <a href="javascript:void(0);" class="map-bar-toggle" style="float: right; padding: 10px">
        <i class="zmdi zmdi-close-circle-o"></i>
    </a>
    <h4 class="">Map Settings</h4>
    <div class="notification-list nicescroll" style="padding: 10px">
        <div class="portlet">
            <div class="portlet-heading portlet-default">
                <h3 class="portlet-title">Examined Dataset</h3>
            </div>
            <div class="panel-body">
                <select name="h_dataset" class="form-control dataset" id="h_dataset">
                    <option value="">Select One</option>
                    <option value=""></option>
                    <option value="_scores">Existing Scores</option>
                    <option value=""></option>
                    @foreach($datasets as $i)
                        <option value="{{$i->id}}" @if(isset($table) && $i->table_name == $table) selected @endif>{{$i->table_title}}</option>
                    @endforeach
                </select>
                <div id="h_datafields">
                    @if(isset($key))
                        @include('citynexus::reports.includes.heatmap._datafields')
                    @endif
                </div>
            </div>
        </div>

        <br>
        <label for="intensity">Intensity</label>
        <input class="text" id="intensity"> </input>
    </div>
</div>
<!-- /Right-bar -->

@push('js_footer')
<script>
    $('.dataset').change(function( event ){
        var selectId = event.currentTarget.id;
        if(selectId == 'v_dataset') var axis = 'v';
        if(selectId == 'h_dataset') var axis = 'h';
        var dataset_id = $('#' + selectId).val();
        $.ajax({
            url: '{{action('\CityNexus\CityNexus\Http\ViewController@getDataFields')}}/' + dataset_id + '/' + axis + '/heatmap',
        }).success(function (data) {
            $('#' + axis + '_datafields').html(data);
        }).error(function (data)
        {
            Command: toastr["warning"]("Uh oh! Something went wrong. Check the console log")
            console.log(data)
        })
    });
</script>
@endpush