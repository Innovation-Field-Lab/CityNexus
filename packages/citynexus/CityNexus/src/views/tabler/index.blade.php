@extends(config('citynexus.template'))

@section(config('citynexus.section'))

    <div class="row">
        <div class="panel panel-default">
            <div class="panel-heading">
                All Data Sets
                <a href="/{{config('citynexus.tabler_root')}}/new?type=form" class="btn btn-primary btn-xs pull-right"><i class="glyphicon glyphicon-plus"></i> New Data Set</a>
            </div>
            <div class="panel-body">
                <table class="table" id="table">
                    <thead>
                    <td>Name</td>
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
                                {{$table->created_at->diffForHumans()}}
                            </td>
                            <td>
                                {{$table->updated_at->diffForHumans()}}
                            </td>
                            <td>
                                <a class="btn btn-sm btn-primary" href="/{{config('citynexus.tabler_root')}}/new-upload/{{$table->id}}">Upload</a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

@stop