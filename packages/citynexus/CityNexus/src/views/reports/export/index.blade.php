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
                            <td><a class="btn btn-primary btn-xs" href="{{action('\CityNexus\CityNexus\Http\ReportController@getDownloadExport', [$export->id])}}"> Download</a></td>
                        </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

@stop