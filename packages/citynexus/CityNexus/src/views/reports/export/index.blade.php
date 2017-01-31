<?php
$pagename = 'All Exports';
$section = 'reports';
?>

@extends(config('citynexus.template'))

@section(config('citynexus.section'))

    <div class="portlet">
        <div class="portlet-body">
            <table class="table table-hover">
                <thead>
                <td>Name</td>
                <td>Updated</td>
                <td>Download</td>
                </thead>
                <tbody>
                @foreach($exports as $export)
                        <tr>
                            <td>{{$export->name}}</td>
                            <td>{{$export->updated_at->diffForHumans()}} <a class="btn btn-primary btn-xs" href="{{action('\CityNexus\CityNexus\Http\ReportController@getRefreshExport', [$export->id])}}"> Refresh</a></td>
                            <td><a class="btn btn-primary btn-xs" href="{{action('\CityNexus\CityNexus\Http\ReportController@getDownloadExport', [$export->id])}}"> Download</a>
                                <button class="btn btn-primary btn-xs" onclick="stataExport({{$export->id}})"> Stata Export</button></td>
                        </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>


    <div id="panel-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content p-0 b-0">
                <div class="panel panel-color panel-primary">
                    <div class="panel-heading">
                        <button type="button" class="close m-t-5" data-dismiss="modal" aria-hidden="true">×</button>
                        <h3 class="panel-title">Panel Primary</h3>
                    </div>
                    <div class="panel-body">
                        <b>Stata Data Import</b>
                        <input type="text" class="form-control" value="" id="stataImport">
                    </div>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->


@stop

@push('js_footer')

<script>
    var stataExport = function(id)
    {
        $.ajax({
            url: "{{action('\CityNexus\CityNexus\Http\APIController@getExport')}}/" + id,
            type: 'GET',
            success: function(data){
                $('#stataImport').val('{{action('\CityNexus\CityNexus\Http\APIController@getRequest')}}?request=' + data);
                $('#panel-modal').modal("show");
            }
        })
    }
</script>

@endpush