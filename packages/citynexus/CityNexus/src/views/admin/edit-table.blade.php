
<?php
        if(isset($tableRecord))
            {
                $pagename = 'TABLE: ' . $tableRecord->table_title;

            }
        else
            {
                $pagename = 'TABLE: ' . $table_name;
            }
            $section = 'tabler';

?>

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
            @if(isset($tableRecord)) @can('superadmin') <a class="btn btn-primary btn-sm" href="{{action('\CityNexus\CityNexus\Http\AdminController@getProcessData', [$table_name])}}"><i class="glyphicon glyphicon-refresh"></i> Reprocess Full Table</a>@endcan @endif

        </div>
        <div class="panel-body" style="overflow: scroll;">
            {!! $table->appends(Input::except('page'))->render() !!}
            @if(count($table) > 0)
            <table class="table table-bordered table-striped">
                <tr>
                    <th></th>
                    @foreach($table[0] as $k => $i)
                        <th>
                            <a href="{{Request::url()}}?sort_by={{$k}}">{{$k}}</a>
                        </th>
                    @endforeach
                </tr>
                <tbody>
                @foreach($table as $row)


                        <tr>
                        <td>
                        @if(isset($row->id))

                        <div class="btn-group">
                            <button type="button" class="btn btn-default dropdown-toggle waves-effect" data-toggle="dropdown" aria-expanded="false"> Actions <span class="caret"></span> </button>
                            <ul class="dropdown-menu">
                                @can('citynexus', ['dataset', 'relink'])<li><a href="{{action('\CityNexus\CityNexus\Http\TablerController@getRelinkRecord', [$table_name, $row->id])}}">Unlink</a></li> @endcan
                                @can('citynexus', ['dataset', 'edit'])<li><a href="{{action('\CityNexus\CityNexus\Http\AdminController@getProcessData', [$table_name, $row->id])}}">Process Row</a></li> @endcan
                                @can('citynexus', ['dataset', 'delete'])<li> <a href="/{{config('citynexus.root_directory')}}/admin/remove-data?table_name={{$table_name}}&row_id={{$row->id}}&_token={{csrf_token()}}">Delete </a></li>@endcan

                            </ul>
                        </div>
                        @endif

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
            {!! $table->appends(Input::except('page'))->render() !!}
        </div>
    </div>

    @stop