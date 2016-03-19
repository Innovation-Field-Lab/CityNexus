@extends(config('citynexus.template'))

@section(config('citynexus.section'))

    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">
                Rankings of {{$score->name}}
            </div>
        </div>
        <div class="panel-body">
            <div id="loading" class="centered">
                Loading <i class="glyphicon glyphicon-hourglass"></i>
            </div>
            <table class="table table-bordered hidden" id="properties-table">
                <thead>
                <tr>
                    <th>Score</th>
                    <th>Address</th>
                    <th>Property ID</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach($properties as $item)
                    <tr>
                        <td>{{$item->score}}</td>
                        <td>{{ucwords($item->full_address)}}</td>
                        <td>{{$item->property_id}}</td>
                        <td><a class="btn btn-sm btn-primary" href="/{{config('citynexus.root_directory')}}/property/{{$item->property_id}}">Property Details</a></td>
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
        $('#properties-table').DataTable(
                {
                    "order": [[0, 'desc']]
                }
        );
        $('#loading').addClass('hidden');
        $('#properties-table').removeClass('hidden');
    } );
</script>
@endpush

