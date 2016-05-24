<?php
$pagename = 'Report Views';
$section = 'reports';
?>

@extends(config('citynexus.template'))

@section(config('citynexus.section'))

    <div class="row">
        <div class="col-sm-12">
            <div class="card-box table-responsive">
            <div id="loading" class="centered">
                Loading <i class="glyphicon glyphicon-hourglass"></i>
            </div>
            <table class="table table-bordered hidden" id="table">
                <thead>
                <tr>
                    <th>Name</th>
                    <th>Report Type</th>
                    <th>Created</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach($views as $item)
                    <tr>
                        <td>{{$item->name}}</td>
                        <td>{{$item->setting->type}}</td>
                        <td>{{$item->updated_at->diffForHumans()}}</td>
                        <td><a class="btn btn-primary" href="{{action('\CityNexus\CityNexus\Http\ViewController@getShow', ['id' => $item->id])}}">Visit</a></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

@stop

@push('style')
        <link href="/vendor/citynexus/plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
@endpush

@push('js_footer')
        <script src="/vendor/citynexus/plugins/datatables/jquery.dataTables.min.js"></script>
<script>

    $(document).ready(function() {
        $('#table').DataTable(
                {
                    "order": [[0, 'desc']]
                }
        );
        $('#loading').addClass('hidden');
        $('#table').removeClass('hidden');
    } );
</script>
@endpush

