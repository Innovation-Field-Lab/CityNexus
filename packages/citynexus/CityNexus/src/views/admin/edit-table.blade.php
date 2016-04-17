
@extends(config('citynexus.template'))

@section(config('citynexus.section'))

    <div class="panel panel-default">
        <div class="panel-heading" style="height: 60px">
            <form action="/{{config('citynexus.tabler_root')}}/view-table" class="form-inline pull-right">
                {{csrf_field()}}
                <select name="table_name" id="table_name" class="form-control">
                    <option value="">Select One</option>
                    @foreach($tables as $i)
                        <option value="{{$i->table_name}}">{{$i->table_name}}</option>
                    @endforeach
                </select>
                <input type="submit" class="btn btn-primary" value="Refresh">
            </form>

            <span class="panel-title">TABLE: {{$table_name}}</span> <br>
            <a  href="/{{config('citynexus.tabler_root')}}/download-table/{{$table_name}}"><i class="glyphicon glyphicon-download"></i> Download CSV</a>
        </div>
        <div class="panel-body" style="overflow: scroll;">
            {!! $table->render() !!}
            @if(count($table) > 0)
            <table class="table table-bordered table-striped">
                <tr>
                    <th></th>
                    @foreach($table[0] as $k => $i)
                        <th>
                            <a href="{{Request::url()}}?sort_by={{$k}}">{{$k}}s</a>
                        </th>
                    @endforeach
                </tr>
                <tbody>
                @foreach($table as $row)
                    <tr>
                        <td>
                            <a href="/{{config('citynexus.root_directory')}}/admin/remove-data?table_name={{$table_name}}&row_id={{$row->id}}&_token={{csrf_token()}}" class="btn btn-primary btn-sm">Delete</a>
                        </td>
                        @foreach($row as $item)
                            <td>
                                {{$item}}
                            </td>
                        @endforeach

                    </tr>
                @endforeach
                </tbody>
            </table>
                @else
            <div class="alert alert-info">
                Table currently holds no records.
            </div>
                @endif
            {!! $table->render() !!}
        </div>
    </div>

    @stop