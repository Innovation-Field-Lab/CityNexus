@extends(config('citynexus.template'))

@section(config('citynexus.section'))

    <div class="row">
        <div class="panel panel-default">
            <div class="panel-heading">
                All Data Sets
                <a href="/{{config('citynexus.tabler_root')}}/uploader" class="btn btn-primary btn-xs pull-right"><i class="glyphicon glyphicon-plus"></i> New Data Set</a>
            </div>
            <div class="alert alert-info"> An error has been found in editing tables, it has been temporarily disabled.  Uploading still works.</div>
            <div class="panel-body">
                <table class="table" id="table">
                    <thead>
                    <td>Name</td>
                    <td>Record Count</td>
                    <td>Created</td>
                    <td>Last Upload</td>
                    <td></td>
                    </thead>
                    <tbody>
                    @foreach($tables as $table)
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
                                {{$table->updated_at->diffForHumans()}}
                            </td>
                            <td>
                                <a class="btn btn-sm btn-primary" href="/{{config('citynexus.tabler_root')}}/new-upload/{{$table->id}}">Upload</a>
                                <a class="btn btn-sm btn-primary" href="/{{config('citynexus.tabler_root')}}/show-table/{{$table->id}}">View</a>
                                <a class="btn btn-sm btn-danger" href="/{{config('citynexus.tabler_root')}}/remove-table/{{$table->id}}"><i class="glyphicon glyphicon-trash"></i></a>
                                <a class="btn btn-sm btn-warning" href="/{{config('citynexus.tabler_root')}}/rollback/{{$table->id}}">Rollback</a>

                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

@stop