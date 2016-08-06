@extends(config('citynexus.template'))

@section(config('citynexus.section'))

    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">
                Create Property Report
            </div>
        </div>
        <div class="panel-body">
            <div class="col-sm-12">
                <form id="form" method="post" action="{{action('\CityNexus\CityNexus\Http\ReportController@postCreateProperty')}}" class="form-horizontal">
                    {!! csrf_field() !!}
                    <input type="hidden" name="type" value="property_report">

                    <div class="form-group">
                        <label for="name" class="control-label col-sm-3">Report Name</label>

                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="name" name="name" value="{{old('name')}}"/>
                        </div>
                    </div>
                    <div class="well">
                        <h4>Property Information</h4>
                        <div class="col-sm-6">
                            <input type="checkbox" name="settings[property_info][citynexus_id]" id="settings[property_info][citynexus_id]"> <label for="settings[property_info][citynexu_id]">CityNexus ID</label>
                        </div>
                        <div class="col-sm-6">
                            <input type="checkbox" name="settings[property_info][lat_long]" id="settings[property_info][lat_long]"> <label for="settings[property_info][lat_long]">Geocoordinates</label>
                        </div>
                        <br>
                    </div>
                    <div class="well">
                        <h4>Datasets</h4>
                        <div class="panel-body">
                            @foreach($datasets as $dataset)
                                <div class="panel-heading " role="tab" id="{{$dataset->table_name}}_heading">
                                    <h4 class="panel-title">
                                        <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#{{$dataset->table_name}}_detail" aria-expanded="false" aria-controls="collapseTwo">
                                            {{$dataset->table_title}}
                                        </a>
                                    </h4>
                                </div>
                                <div id="{{$dataset->table_name}}_detail" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo">
                                    <div class="list-group">
                                        <div class="list-group-item"><input type="checkbox" class="dataset-{{$dataset->id}}" id="dataset-{{$dataset->id}}" onchange="selectAllDataset({{$dataset->id}})"> <b>Select All in Dataset</b></div>
                                        <div class="list-group-item"><input type="checkbox" name="settings[datasets][{{$dataset->table_name}}][created_at]" class="dataset-{{$dataset->id}}" value="true"> Time Stamp </div>

                                        @foreach($dataset->schema as $column)
                                            @if(isset($column->show) && $column->show == true)
                                                <div class="list-group-item"><input type="checkbox" name="settings[datasets][{{$dataset->table_name}}][{{$column->key}}]" class="dataset-{{$dataset->id}}" value="true"> {{$column->name}} </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach


                        </div>
                    </div>
                    <br>
                    <input type="submit" class="btn btn-primary" value="Create">
                </form>
            </div>
        </div>
   
    </div>

@stop


@push('js_footer')

<script>
    function selectAllDataset( id )
    {

       if($("#dataset-" + id).attr('checked') === 'checked')
       {
           $(".dataset-" + id).removeAttr('checked');
       }
        else
       {
           $(".dataset-" + id).attr('checked', 'checked');
       }
    }
</script>
@endpush