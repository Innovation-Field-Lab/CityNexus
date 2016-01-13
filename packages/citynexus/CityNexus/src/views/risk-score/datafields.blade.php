<div class="well">
    <h4>Select Data Field</h4>
    <input type="hidden" id="table_id" value="{{$dataset->id}}">
    <select name="" class="form-control" id="datafield">
        <option value="">[Select Data Field]</option>
        @foreach($scheme as $i)
            <option value="{{$i->key}}">{{$i->name}}</option>
        @endforeach 
    </select>
    <div id="fieldsettings"></div>
</div>

<script>
    $('#datafield').change(function(){
        var key = $('#datafield').val();
        var dataset_id = $('#table_id').val();
        $.ajax({
            url: '/{{config("citynexus.root_directory")}}/risk-score/data-field/',
            type: 'get',
            data: {
                _token: "{{csrf_token()}}",
                key: key,
                table_id: table_id
            }
        }).success(function(data){
            $('#fieldsettings').html(data);
        });
    })
</script>