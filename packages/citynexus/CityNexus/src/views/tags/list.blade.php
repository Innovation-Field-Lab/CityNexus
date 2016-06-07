<?php
$pagename = 'All Tagged Properties: ' . $tag->tag;
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
                            {{--<th>Added By</th>--}}
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($tag->properties as $item)
                            <tr>
                                <td>{{$item->id}}</td>
                                <td>{{ucwords($item->full_address)}}</td>
                                {{--<td>@if(isset($item->pivot->created_by)){{\App\User::find($item->pivot->created_by)->full_name}}@endif</td>--}}
                                <td>
                                    @can('citynexus', ['group' => 'properties', 'method' => 'show'])
                                    <a class="btn btn-sm btn-primary" href="{{action('\CityNexus\CityNexus\Http\PropertyController@getShow', ['id' => $item->id])}}">Details</a>
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
        <script src="https://cdn.datatables.net/buttons/1.2.0/js/dataTables.buttons.min.js"></script>
        <script src="//cdn.datatables.net/buttons/1.2.0/js/buttons.print.min.js"></script>


        <script type="text/javascript">
            $(document).ready(function() {
                $('#datatable').dataTable({
                    stateSave: true,
                    dom: 'Bfrtip',
                    buttons: [
                        'print'
                    ]
                });
                $('#loading').addClass('hidden');
                $('#table-wrapper').removeClass('hidden');
            } );
            TableManageButtons.init();

        </script>
    @endpush

