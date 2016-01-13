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
                    <th>House Number</th>
                    <th>Street Name</th>
                    <th>Street Type</th>
                    <th>Unit</th>
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
            columns: [
                { data: 'id', name: 'id' },
                { data: 'house_number', name: 'house_number' },
                { data: 'street_name', name: 'street_name' },
                { data: 'street_type', name: 'street_type' },
                { data: 'unit', name: 'unit' },
            ]
        });
    });
</script>
@endpush

