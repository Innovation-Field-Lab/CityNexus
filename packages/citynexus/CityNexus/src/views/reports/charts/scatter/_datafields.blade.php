@if(isset($scores))
    <br>
    <b>Select Existing Score</b>
    <select id="{{$axis}}_table_name" class="form-control" id="datafield">
        <option value="">[Select Score]</option>
        @foreach($scores as $i)
            <option value="citynexus_scores_{{$i->id}}">{{$i->name}}</option>
        @endforeach
    </select>
    <input type='hidden' id="{{$axis}}_datafield" value="score">
@else
    <input type="hidden" id="{{$axis}}_table_id" value="{{$dataset->id}}">
    <input type="hidden" id="{{$axis}}_table_title" value="{{$dataset->table_title}}">
    <input type="hidden" id="{{$axis}}_table_name" value="{{$dataset->table_name}}">
    <br>
    <b>Select Data Field</b>
    <select name="{{$axis}}_data[datafield]" class="form-control" id="{{$axis}}_datafield">
        <option value="">[Select Data Field]</option>
        @foreach($scheme as $i)
            @if($i->type == 'integer' or $i->type == 'float')
            <option value="{{$i->key}}">{{$i->name}}</option>
            @endif
        @endforeach
    </select>
@endif

@if($axis == 'v')

    <script>
        var hTable = $('#h_table_name');
        var hKey = $('#h_datafield');
        var vTable = $('#v_table_name');
        var vKey = $('#v_datafield');

        vKey.change(function()
        {
            if(hKey.val() != null){
                refreshChart();
            }
        });

        hKey.change(function()
        {
            if(vKey.val() != null){
                refreshChart();
            }
        });

        function refreshChart() {
            drawChart(hTable.val(), hKey.val(), vTable.val(), vKey.val());
        }
    </script>
@endif