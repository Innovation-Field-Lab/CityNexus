<?php
    $pagename = 'All Properties';
    $section = 'properties';
?>

@extends(config('citynexus.template'))

@section(config('citynexus.section'))

    <div class="row">
        <div class="col-sm-12">
            <div class="card-box table-responsive">
                {{--<div class="dropdown pull-right">--}}
                    {{--<a href="#" class="dropdown-toggle card-drop" data-toggle="dropdown" aria-expanded="false">--}}
                        {{--<i class="zmdi zmdi-more-vert"></i>--}}
                    {{--</a>--}}
                    {{--<ul class="dropdown-menu" role="menu">--}}
                        {{--<li><a href="#">Action</a></li>--}}
                        {{--<li><a href="#">Another action</a></li>--}}
                        {{--<li><a href="#">Something else here</a></li>--}}
                        {{--<li class="divider"></li>--}}
                        {{--<li><a href="#">Separated link</a></li>--}}
                    {{--</ul>--}}
                {{--</div>--}}

                <div id="loading">
                    Loading <i class="fa fa-spinner fa-spin"></i>
                </div>
                <div id="table-wrapper" class="hidden">
                    <table id="datatable" class="table table-striped table-bordered">
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
                            <td>
                                @can('citynexus', ['group' => 'properties', 'method' => 'show'])
                                <a class="btn btn-sm btn-primary" href="{{action('\CityNexus\CityNexus\Http\PropertyController@getShow', ['id' => $item->id])}}">Details</a>
                                @endcan
                                @can('citynexus', ['group' => 'properties', 'method' => 'merge'])
                                <a class="btn btn-sm btn-primary" href="{{action('\CityNexus\CityNexus\Http\TablerController@getMergeRecords')}}/{{$item->id}}">Merge Property</a>
                                @endcan
                            </td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
        </div>
    </div>

@stop

@push('style')
        <!-- DataTables -->
    <link href="/vendor/citynexus/plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
@endpush

@push('js_footer')
<script src="/vendor/citynexus/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="/vendor/citynexus/plugins/datatables/dataTables.bootstrap.js"></script>

<script src="/vendor/citynexus/pages/datatables.init.js"></script>

<script type="text/javascript">
    $(document).ready(function() {
        $('#datatable').dataTable({
            stateSave: true
        });
        $('#loading').addClass('hidden');
        $('#table-wrapper').removeClass('hidden');
    } );
    TableManageButtons.init();

</script>
@endpush

