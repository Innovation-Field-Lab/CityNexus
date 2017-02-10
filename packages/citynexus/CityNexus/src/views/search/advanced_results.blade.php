<?php
$pagename = 'Search Results';
$section = 'search';
?>

@extends(config('citynexus.template'))

@section(config('citynexus.section'))

    <div class="row">
        <div class="col-sm-12">
            <button class="btn btn-primary waves-effect waves-light" data-toggle="modal" data-target="#saveResults">Save Results</button>
        </div>
    </div>


    <div class="portlet">
        {{--<div class="portlet-heading portlet-default">--}}
        {{--<div class="portlet-widgets">--}}
        {{--<a href="javascript:;" data-toggle="reload"><i class="zmdi zmdi-refresh"></i></a>--}}
        {{--<a data-toggle="collapse" data-parent="#accordion1" href="#bg-primary"><i class="zmdi zmdi-minus"></i></a>--}}
        {{--<a href="#" data-toggle="remove"><i class="zmdi zmdi-close"></i></a>--}}
        {{--</div>--}}
        {{--<div class="clearfix"></div>--}}
        {{--</div>--}}
        <div class="portlet-body">

            @if($results->count() != 0)
                <table id="datatable" class="table table-striped table-bordered">
                    <thead>
                    <tr>
                        <th>House Number</th>
                        <th>Street</th>
                        <th>Unit</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($results as $item)
                        <tr>
                            <td>{{ucwords($item->house_number)}}</td>
                            <td>{{ucwords($item->street_name . ' ' . $item->street_type)}}</td>
                            <td>{{ucwords($item->unit)}}</td>
                            <td>
                                @can('citynexus', ['group' => 'properties', 'method' => 'show'])
                                    <a class="btn btn-sm btn-primary" href="{{action('\CityNexus\CityNexus\Http\PropertyController@getShow', ['id' => $item->id])}}">Details</a>
                                @endcan
                                @can('citynexus', ['group' => 'properties', 'method' => 'merge'])
                                    <a class="btn btn-sm btn-primary" href="{{action('\CityNexus\CityNexus\Http\TablerController@getMergeRecords', [$item->id])}}">Merge Property</a>
                                @endcan
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @else
                <div class="alert alert-info">
                    No properties found!
                </div>
            @endif
        </div>
    </div>

    <div id="saveResults" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content p-0">
                <ul class="nav nav-tabs navtab-bg nav-justified">
                    <li class="active">
                        <a href="#searchTab" data-toggle="tab" aria-expanded="true">
                            <span class="visible-xs"><i class="fa fa-home"></i></span>
                            <span class="hidden-xs">Save Search</span>
                        </a>
                    </li>
                    <li class="">
                        <a href="#exportTab" data-toggle="tab" aria-expanded="false">
                            <span class="visible-xs"><i class="fa fa-user"></i></span>
                            <span class="hidden-xs">Save as an Export</span>
                        </a>
                    </li>
                    <li class="">
                        <a href="#tagsTab" data-toggle="tab" aria-expanded="false">
                            <span class="visible-xs"><i class="fa fa-envelope-o"></i></span>
                            <span class="hidden-xs">Tag all results</span>
                        </a>
                    </li>
                </ul>
                <form action="{{action('\CityNexus\CityNexus\Http\SearchController@postSaveSearch')}}" method="post">
                    {!! csrf_field() !!}
                    <input type="hidden" name="filters" value="{{json_encode($filters)}}">
                    <div class="tab-content">
                        <div class="tab-pane active" id="searchTab">
                            <div>
                                <label for="name">Name Search</label>
                                <input type="text" class="form-control" name="search">
                                <br>
                                <button name="type" value="search" class="btn btn-primary">Save Search</button>
                            </div>
                        </div>
                        <div class="tab-pane" id="exportTab">
                            <label for="name">Name Export</label>
                            <input type="text" class="form-control" name="export">
                            <br>
                            <button name="type" value="export" class="btn btn-primary">Save Export</button>
                        </div>
                        <div class="tab-pane" id="tagsTab">
                            <label for="name">Add Tags</label><br>
                            <div id="new-tag-input">
                                <select class="form-control typeahead" style="width: 100%" name="tags[]" id="tag" multiple></select>
                            </div>
                            <br>
                            <button name="type" value="tags" class="btn btn-primary">Add Tags</button>
                        </div>
                    </div>
                </form>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

@stop

@push('style')
<!-- DataTables -->
<link href="/vendor/citynexus/plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.css">

@endpush

@push('js_footer')
<script src="/vendor/citynexus/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="/vendor/citynexus/plugins/datatables/dataTables.bootstrap.js"></script>

<script src="/vendor/citynexus/pages/datatables.init.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.full.min.js"></script>

<script type="text/javascript">
    $(document).ready(function() {
        $('#datatable').dataTable({
            stateSave: true
        });
        $('#loading').addClass('hidden');
        $('#table-wrapper').removeClass('hidden');
    } );
    TableManageButtons.init();

    var tags = {!! json_encode($tags) !!};

    $('#tag').select2({
        data: tags
    });
</script>
@endpush

