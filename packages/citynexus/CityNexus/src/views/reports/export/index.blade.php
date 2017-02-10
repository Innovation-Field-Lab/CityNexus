<?php
$pagename = 'All Exports';
$section = 'exports';
?>

@extends(config('citynexus.template'))

@section(config('citynexus.section'))

    <div class="portlet">
        <div class="portlet-body">

            {!! $exports->render() !!}

            <table class="table table-hover">
                <thead>
                <td>Name</td>
                <td>Updated</td>
                <td>Export</td>
                <td></td>
                </thead>
                <tbody>
                @foreach($exports as $export)
                        <tr id="export-{{$export->id}}">
                            <td>{{$export->name}}</td>
                            <td>{{$export->updated_at->diffForHumans()}} </td>
                            <td>
                                <a class="btn btn-primary btn-xs" href="{{action('\CityNexus\CityNexus\Http\ReportController@getDownloadExport', [$export->id])}}"> Download CSV</a>
                                <button class="btn btn-primary btn-xs" onclick="jsonExport({{$export->id}})"> JSON Export</button>
                            </td>
                            <td>
                                @unless(isset($export->elements['_type']) && $export->elements['_type'] == 'saved_search')
                                <a class="btn btn-primary btn-xs" href="{{action('\CityNexus\CityNexus\Http\ReportController@getRefreshExport', [$export->id])}}">Refresh <icon class="fa fa-refresh"></icon></a>
                                @endunless
                                <button class="btn btn-danger btn-xs" onclick="deleteExport({{$export->id}})"><icon class="fa fa-trash"></icon></button>
                            </td>
                        </tr>
                @endforeach
                </tbody>
            </table>

            {!! $exports->render() !!}

        </div>
    </div>


    <div id="panel-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content p-0 b-0">
                <div class="panel panel-color panel-primary">
                    <div class="panel-heading">
                        <button type="button" class="close m-t-5" data-dismiss="modal" aria-hidden="true">×</button>
                        <h3 class="panel-title">JSON Export Request</h3>
                    </div>
                    <div class="panel-body">
                        <b>GET REQUEST:</b>
                        <input type="text" class="form-control" value="" id="stataImport">
                    </div>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->


@stop

@push('js_footer')

<script>
    var jsonExport = function(id)
    {
        $.ajax({
            url: "{{action('\CityNexus\CityNexus\Http\APIController@getExport')}}/" + id,
            type: 'GET',
            success: function(data){
                $('#stataImport').val('{{action('\CityNexus\CityNexus\Http\APIController@getRequest')}}/' + data);
                $('#panel-modal').modal("show");
            }
        })
    }

    var deleteExport = function(id)
    {
        $.ajax({
            url: "{{action('\CityNexus\CityNexus\Http\ReportController@postDeleteExport')}}",
            type: "post",
            data: {
                _token: "{{csrf_token()}}",
                export_id: id
            },
            success: function(){
                $('#export-' + id).remove();
            },
            error: function(){
                alert('Uh oh. Something went wrong.');
            }
        })
    }
</script>

@endpush