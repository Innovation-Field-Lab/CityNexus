@extends(config('citynexus.template'))

@section(config('citynexus.section'))

    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">
                All Properties
            </div>
        </div>
        <div class="panel-body">
            <table class="table table-bordered" id="properties-table">
                <thead>
                <tr>
                    <th>Id</th>
                    <th>Address</th>
                    <th></th>
                </tr>
                </thead>
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
    $(function() {
        $('#properties-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: '/citynexus/properties-data/',
            buttons:['excel', 'print'],
            columns: [
                { data: 'id', name: 'ID' },
                { data: 'full_address', name: 'Full Name' },
                {
                    "mData": null,
                    "bSortable": false,
                    "mRender": function (o) { return '<a class="btn btn-sm btn-primary" href="/{{config('citynexus.root_directory')}}/property?property_id=' + o.id + '">' + 'Details' + '</a>'; }
                }
            ]
        });
    });
</script>
@endpush

