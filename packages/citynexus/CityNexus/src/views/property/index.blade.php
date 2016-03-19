@extends(config('citynexus.template'))

@section(config('citynexus.section'))

    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">
                All Properties
            </div>
        </div>
        <div class="panel-body">
            <div id="loading" class="centered">
                Loading <i class="glyphicon glyphicon-hourglass"></i>
            </div>
            <table class="table table-bordered hidden" id="properties-table">
                <thead>
                <tr>
                    <th>Id</th>
                    <th>Address</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach($properties as $item)
                <tr>
                    <td>{{$item->id}}</td>
                    <td>{{ucwords($item->full_address)}}</td>
                    <td><a class="btn btn-sm btn-primary" href="/{{config('citynexus.root_directory')}}/property/{{$item->id}}">Details</a> <a class="btn btn-sm btn-primary" href="{{action('\CityNexus\CityNexus\Http\TablerController@getMergeRecords')}}/{{$item->id}}">Merge Property</a>
                    </td>

                </tr>
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
        $('#properties-table').DataTable();
        $('#loading').addClass('hidden');
        $('#properties-table').removeClass('hidden');
    } );

    {{--$(function() {--}}
        {{--$('#properties-table').DataTable({--}}
            {{--processing: true,--}}
            {{--serverSide: true,--}}
            {{--ajax: '/citynexus/properties-data/',--}}
            {{--buttons:['excel', 'print'],--}}
            {{--columns: [--}}
                {{--{ data: 'id', name: 'ID' },--}}
                {{--{ data: 'full_address', name: 'Full Name' },--}}
                {{--{--}}
                    {{--"mData": null,--}}
                    {{--"bSortable": false,--}}
                    {{--"mRender": function (o) { return '<a class="btn btn-sm btn-primary" href="/{{config('citynexus.root_directory')}}/property?property_id=' + o.id + '">' + 'Details' + '</a>'; }--}}
                {{--}--}}
            {{--]--}}
        {{--});--}}
    {{--});--}}
</script>
@endpush

