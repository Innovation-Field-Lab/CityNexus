<?php
$pagename = 'All Data Sets';
$section = 'datasets';
?>

@extends(config('citynexus.template'))

@section(config('citynexus.section'))

    <div class="portlet">
        {{--<div class="portlet-heading portlet-default">--}}
        {{--<div class="portlet-widgets">--}}
        {{--<a href="javascript:;" data-toggle="reload"><i class="zmdi zmdi-refresh"></i></a>--}}
        {{--<a data-toggle="collapse" data-parent="#accordion1" href="#bg-primary"><i class="zmdi zmdi-minus"></i></a>--}}
        {{--<a href="#" data-toggle="remove"><i class="zmdi zmdi-close"></i></a>--}}
        {{--</div>--}}
        {{--<div class="clearfix"></div>--}}
        {{--</div>--}}

        <div class="portlet-body">
            <table class="table table-hover">
                    <thead>
                    <td>Name</td>
                    <td>Record Count</td>
                    <td>Created</td>
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
                                {{$table->created_at->diffForHumans()}}
                            </td>
                            <td>
                                {{$table->updated_at->formatLocalized('%B %d, %Y')}}
                            </td>
                            <td>
                                @can('citynexus', ['datasets', 'upload'])
                                <a class="btn btn-sm btn-primary" href="/{{config('citynexus.tabler_root')}}/new-upload/{{$table->id}}">Upload</a>
                                @endcan
                                @can('citynexus', ['datasets', 'view'])
                                <a class="btn btn-sm btn-primary" href="/{{config('citynexus.tabler_root')}}/show-table/{{$table->table_name}}">View</a>
                                @endcan
                                @can('citynexus', ['datasets', 'edit'])
                                <a class="btn btn-sm btn-primary" href="/{{config('citynexus.tabler_root')}}/edit-table/{{$table->id}}">Edit</a>
                                @endcan
                                @can('citynexus', ['datasets', 'delete'])
                                <a class="btn btn-sm btn-danger" href="/{{config('citynexus.tabler_root')}}/remove-table/{{$table->id}}"><i class="glyphicon glyphicon-trash"></i></a>
                                @endcan
                                @can('citynexus', ['datasets', 'rollback'])
                                <a class="btn btn-sm btn-warning" href="/{{config('citynexus.tabler_root')}}/rollback/{{$table->id}}">Rollback</a>
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