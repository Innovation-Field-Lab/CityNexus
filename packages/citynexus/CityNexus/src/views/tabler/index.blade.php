<?php
$pagename = 'All Data Sets';
$section = 'datasets';
?>

@extends(config('citynexus.template'))

@section(config('citynexus.section'))

    <div class="portlet">
        <div class="portlet-body">
            <table class="table table-hover">
                    <thead>
                    <td>Name</td>
                    <td>Record Count</td>
                    <td>Last Upload</td>
                    <td></td>
                    </thead>
                    <tbody>
                    @foreach($tables as $table)
                        @if($table->table_title != null)
                        <tr>
                            <td>
                                {{$table->table_title}}
                            </td>
                            <td>
                                {{\Illuminate\Support\Facades\DB::table($table->table_name)->count()}}
                            </td>
                            <td>
                                {{$table->updated_at->diffForHumans()}}
                            </td>
                            <td>
                                @can('citynexus', ['datasets', 'view'])
                                <a class="btn btn-sm btn-primary" href="/{{config('citynexus.tabler_root')}}/show-table/{{$table->table_name}}">View</a>
                                @endcan
                                @can('citynexus', ['datasets', 'upload'])
                                <div class="btn-group">
                                    <button type="button" class="btn btn-default dropdown-toggle waves-effect" data-toggle="dropdown" aria-expanded="false"> Actions <span class="caret"></span> </button>
                                    <ul class="dropdown-menu">
                                        <li><a href="/{{config('citynexus.tabler_root')}}/new-upload/{{$table->id}}">Upload</a></li>
                                        <li><a href="{{action('\CityNexus\CityNexus\Http\DatasetController@getDropboxSync', [$table->id])}}">Dropbox Sync</a></li>
                                        @can('citynexus', ['datasets', 'edit'])
                                        <li><a href="{{action('\CityNexus\CityNexus\Http\TablerController@getEditTable', [$table->id])}}">Edit Table</a></li>
                                        @endcan
                                        @can('citynexus', ['datasets', 'rollback'])
                                        <li><a href="{{action('\CityNexus\CityNexus\Http\TablerController@getRollback', [$table->id])}}">Upload History</a></li>
                                        @endcan
                                        @can('citynexus', ['datasets', 'delete'])
                                        <li><a href="{{action('\CityNexus\CityNexus\Http\TablerController@getRemoveTable', [$table->id])}}">Hide Table</a></li>
                                        @endcan
                                    </ul>
                                </div>
                                @endcan
                            </td>
                        </tr>
                        @endif
                    @endforeach
                    </tbody>
                </table>
            </div>
    </div>

@stop