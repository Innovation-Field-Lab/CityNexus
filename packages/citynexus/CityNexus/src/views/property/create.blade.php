<?php
$pagename = 'Create New Property';
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

                <form method="post" class="form-horizontal" action="{{action('\CityNexus\CityNexus\Http\PropertyController@postCreate')}}">
                    {!! csrf_field() !!}
                    <div class="form-group">
                        <label for="house_number" class="control-label col-sm-3">House Number</label>

                        <div class="col-sm-9">
                            <input type="number" class="form-control address" id="house_number" name="house_number"
                                   value="{{old('house_number')}}" onchange="updateFullAddress()" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="street_name" class="control-label col-sm-3">Street Name</label>

                        <div class="col-sm-9">
                            <input type="text" class="form-control address" id="street_name" name="street_name"
                                   value="{{old('street_name')}}"/ onchange="updateFullAddress()">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="street_type" class="control-label col-sm-3">Street Type</label>

                        <div class="col-sm-9">

                            <select name="street_type" id="street_type" class="form-control address" onchange="updateFullAddress()">
                                <option value="">Select One</option>
                                <option value="alley" @if(old('street_type') == 'alley') selected @endif>Alley</option>
                                <option value="avenue" @if(old('street_type') == 'aveneu') selected @endif>Avenue</option>
                                <option value="boulevard" @if(old('street_type') == 'boulevard') selected @endif>Boulevard</option>
                                <option value="circle" @if(old('street_type') == 'circle') selected @endif>Circle</option>
                                <option value="court" @if(old('street_type') == 'court') selected @endif>Court</option>
                                <option value="drive" @if(old('street_type') == 'drive') selected @endif>Drive</option>
                                <option value="expressway" @if(old('street_type') == 'expressway') selected @endif>Express Way</option>
                                <option value="lane" @if(old('street_type') == 'lane') selected @endif>Lane</option>
                                <option value="parkway" @if(old('street_type') == 'parkway') selected @endif>Parkway</option>
                                <option value="place" @if(old('street_type') == 'place') selected @endif>Place</option>
                                <option value="road" @if(old('street_type') == 'road') selected @endif>Road</option>
                                <option value="row" @if(old('street_type') == 'row') selected @endif>Row</option>
                                <option value="street" @if(old('street_type') == 'street') selected @endif>Street</option>
                                <option value="square" @if(old('street_type') == 'square') selected @endif>Square</option>
                                <option value="terrace" @if(old('street_type') == 'terrace') selected @endif>Terrace</option>
                                <option value="way" @if(old('street_type') == 'way') selected @endif>Way</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="unit" class="control-label col-sm-3">Unit</label>

                        <div class="col-sm-9">
                            <input type="text" class="form-control address" id="unit" name="unit" value="{{old('unit')}}" onchange="updateFullAddress()"/>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="full_address" class="control-label col-sm-3">Full Address</label>

                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="full_address_preview" name="full_address"
                                   value="{{old('full_address')}}" disabled/>
                            <input type="hidden" class="form-control" id="full_address" name="full_address"
                                   value="{{old('full_address')}}" />
                        </div>
                    </div>

                    <input type="submit" value="Create Property" class="btn btn-primary">
                </form>
                
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

    @push('js_footer')
        <script>
            var houseNumber = $('#house_number');
            var streetName = $('#street_name');
            var streetType = $('#street_type');
            var unit = $('#unit');
            function updateFullAddress(){
                        var fullAddress = '';
                        if(houseNumber.val() != '') var fullAddress = fullAddress.concat(' ' + houseNumber.val());
                        if(streetName.val() != '') var fullAddress = fullAddress.concat(' ' + streetName.val());
                        if(streetType.val() != '') var fullAddress = fullAddress.concat(' ' + streetType.val());
                        if(unit.val() != '') var fullAddress = fullAddress.concat(' ' + unit.val());
                        $('#full_address').val(fullAddress.toLowerCase());
                        $('#full_address_preview').val(fullAddress.toUpperCase());
                        };

        </script>
    @endpush
