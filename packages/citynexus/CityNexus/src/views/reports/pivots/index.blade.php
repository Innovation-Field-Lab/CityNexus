@extends(config('citynexus.template'))

@section(config('citynexus.section'))
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">
                Pivot
            </div>
        </div>
        <div class="panel-body">
            <div id="loading" class="centered">
                Loading <i class="glyphicon glyphicon-hourglass"></i>
            </div>
            <table class="table table-bordered hidden" id="datatable">
                <thead>
                <tr>
                    <th>{{ucwords($field)}}</th>
                    <th>Count</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach($results as $item)
                    @if(current($item)->$field != null)
                        <tr>
                            <td><a href="{{action('\CityNexus\CityNexus\Http\ReportController@getPivotProfile')}}?table={{$table}}&field={{$field}}&owner={{current($item)->$field}}">{{current($item)->$field}}</a></td>
                            <td>{{count($item)}}</td>
                            <td></td>
                        </tr>
                    @endif
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

@stop

@push('style')
<link rel="stylesheet" href="//cdn.datatables.net/1.10.10/css/jquery.dataTables.min.css">
@endpush

@push('js_footer')
<script src="//cdn.datatables.net/1.10.10/js/jquery.dataTables.min.js"></script>
<script>

    $(document).ready(function() {
        $('#datatable').DataTable({
            "order": [[1, 'desc']]
        });
        $('#loading').addClass('hidden');
        $('#datatable').removeClass('hidden');
    } );
</script>
@endpush

