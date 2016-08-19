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
                    <th>Rank</th>
                    <th>Score</th>
                    <th>Address</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach($properties as $rank => $item)
                    @if($item->score !== null)
                    <tr>
                        <td>{{$rank + 1}}</td>
                        <td>{{$item->score}}</td>
                        <td>{{ucwords($item->full_address)}}</td>
                        <td><a class="btn btn-sm btn-primary" href="{{action('\CityNexus\CityNexus\Http\PropertyController@getShow', ['id' => $item->property_id])}}">Property Details</a></td>
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
<script src="https://cdn.datatables.net/buttons/1.2.2/js/dataTables.buttons.min.js"></script>
<script src="//cdn.datatables.net/buttons/1.2.2/js/buttons.print.min.js"></script>

<script>

    $(document).ready(function() {
        $('#properties-table').DataTable(
                {
                    order: [[0, 'asc']],
                    dom: 'Bfrtip',
                    buttons: [
                        {
                        extend: 'print',
                        className: 'btn btn-default'

                        },
                        {
                            extend: 'csv',
                            text: 'Copy all data',
                            exportOptions: {
                                modifier: {
                                    search: 'none'
                                }
                            }
                        }

                    ]
                }
        );
        $('#loading').addClass('hidden');
        $('#properties-table').removeClass('hidden');
    } );
</script>
@endpush

