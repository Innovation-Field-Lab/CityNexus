    <input type="hidden" name="table_id" value="{{$dataset->id}}">
    <input type="hidden" name="table_title" value="{{$dataset->table_title}}">
    <input type="hidden" name="table_name" value="{{$dataset->table_name}}">

    <label>Select Data Field</label>
    <select name="" class="form-control" id="datafield">
        <option value="">[Select Data Field]</option>
        @foreach($scheme as $i)
            <option value="{{$i->key}}">{{$i->name}} [{{$i->type}}]</option>
        @endforeach 
    </select>
    <div id="fieldsettings"></div>
</div>

<script>
    $('#datafield').change(function(){
        var key = $('#datafield').val();
        $.ajax({
            url: '/{{config("citynexus.root_directory")}}/risk-score/data-field/',
            type: 'get',
            data: {
                _token: "{{csrf_token()}}",
                key: key,
                table_id: {{$dataset->id}}
            }
        }).success(function(data){
            $('#fieldsettings').html(data);
        });
    })
</script>