@if(isset($scores))
    <br>
    <b>Select Existing Score</b>
    <select id="table_name" class="form-control" id="datafield">
        <option value="">[Select Score]</option>
        @foreach($scores as $i)
            <option value="citynexus_scores_{{$i->id}}">{{$i->name}}</option>
        @endforeach
    </select>
    <input type='hidden' id="datafield" value="score">
@else
    <input type="hidden" id="table_id" value="{{$dataset->id}}">
    <input type="hidden" id="table_title" value="{{$dataset->table_title}}">
    <input type="hidden" id="table_name" value="{{$dataset->table_name}}">
    <br>
    <b>Select Data Field</b>
    <select name="datafield" class="form-control" id="datafield">
        <option value="">[Select Data Field]</option>
        @foreach($scheme as $i)
            @if($i->type == 'integer' or $i->type == 'float')
                <option value="{{$i->key}}">{{$i->name}}</option>
            @endif
        @endforeach
    </select>
@endif

<script>
    $("#datafield").change(function()
    {
        refreshMap($("#table_name").val(), $("#datafield").val() );
    });
</script>