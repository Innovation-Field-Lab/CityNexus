<?php
$pagename = "Heat Map";
$section = "reports";
?>

@extends(config('citynexus.template'))

@section(config('citynexus.section'))

    <div class="col-md-12">
        <div class="alert alert-info">
            CityNexus is in the process of removing heat maps in their current incarnation, replacing them
            with <a href="{{action('\CityNexus\CityNexus\Http\ViewController@getDotMap')}}">Dot Maps</a>.  You can learn
            more about how to use Dot Maps in the <a href="https://citynexus.zendesk.com/hc/en-us/articles/115001033425">CityNexus Help Center</a>.
            If you find that Dot Maps don't replace your need for Heat Maps let us know at <a href="mailto:support@citynexus.org">support@citynexus.org</a>.
        </div>
        <div class="card-box">
            <div class="dropdown pull-right">
                <a href="#" class="dropdown-toggle card-drop" data-toggle="dropdown"
                   aria-expanded="false">
                    <i class="zmdi zmdi-more-vert"></i>
                </a>
                <ul class="dropdown-menu" role="menu">
                    <li><a href="javascript:void(0);" class="map-bar-toggle">Open Map Settings</a></li>
                    @can('citynexus', ['reports', 'save'])
                    @if(!isset($report_id))
                        <li id="save-report-line"><a onclick="saveReport()" id="save-report" style="cursor: pointer"> Save as Report</a></li>
                    @else
                        <li><a onclick="updateReport({{$report_id}})" id="save-report" style="cursor: pointer"> Save Report Updates</a></li>
                    @endif
                    @endcan
                </ul>
            </div>
            <div id="mapid" style="width: 100%; height: 700px"></div>

        </div>
    </div><!-- end col -->

@stop

@push('sidebar')
@include('citynexus::reports.includes.heatmap._map_setting')
@endpush

@push('style')
<link rel="stylesheet" href="/vendor/citynexus/css/leaflet.css"/>
<link rel="stylesheet" href="/vendor/citynexus/css/slider.css"/>
<link href='http://fonts.googleapis.com/css?family=Open+Sans|Fjalla+One' rel='stylesheet' type='text/css'>
<link type="text/css" rel="stylesheet" href="/vendor/citynexus/css/shCoreEclipse.css"/>
<!--[if lte IE 8]>
<link rel="stylesheet" href="/css/leaflet.ie.css" /> -->

<style>
    #map {
        width:100%;
        height:100%;
        margin:0 auto 0px;
    }
    #map a {
        color:initial;
    }
    canvas {
        opacity: 1
    }
    #map-setting
    {
        width: 75px;
        position: fixed;
        height: 40px;
        top: 100px;
        right: 0px;
        background-color: #f2f2f2;
        z-index: 500;
        padding: 10px;
    }
</style>

<link href="/vendor/citynexus/plugins/ion-rangeslider/ion.rangeSlider.css" rel="stylesheet" type="text/css"/>
<link href="/vendor/citynexus/plugins/ion-rangeslider/ion.rangeSlider.skinFlat.css" rel="stylesheet" type="text/css"/>

@endpush

@push('js_footer')
<script src="/vendor/citynexus/js/leaflet.js"></script>
<script type="text/javascript" src="/vendor/citynexus/js/shCore.js"></script>
<script type="text/javascript" src="/vendor/citynexus/js/shBrushJScript.js"></script>

<script type="text/javascript" src="/vendor/citynexus/js/webgl-heatmap.js"></script>
<script type="text/javascript" src="/vendor/citynexus/js/webgl-heatmap-leaflet.js"></script>
<script src="/vendor/citynexus/plugins/ion-rangeslider/ion.rangeSlider.min.js"></script>
<script type="text/javascript">
    @if(!isset($key))
    $('#wrapper').toggleClass('right-bar-enabled');
    @endif
    // right side-bar toggle
    $('.map-bar-toggle').on('click', function (e) {
        $('#wrapper').toggleClass('right-bar-enabled');
    });
    var map = L.map('mapid').setView([{{env('MAP_LAT')}}, {{env('MAP_LONG')}}], {{env('MAP_ZOOM')}});
    L.tileLayer('https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token={accessToken}', {
        attribution: 'Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors, <a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="http://mapbox.com">Mapbox</a>',
        maxZoom: 22,
        id: 'seanalaback.piieb4dh',
        accessToken: 'pk.eyJ1Ijoic2VhbmFsYWJhY2siLCJhIjoiY2ltaTNpbWtrMDA0YnV0a2c3YjQxZ2YxYyJ9.w85abrrlR743J2MVtrROKw'
    }).addTo(map);
    L.control.scale().addTo(map);
    //custom size for this example, and autoresize because map style has a percentage width
    var heatmap = L.webGLHeatmap({
        size: 50,
        units: 'px',
        alphaRange: 0.4
    });
    @if(isset($table) && isset($key))
    $.ajax({
        'url': '{{action('\CityNexus\CityNexus\Http\ViewController@getHeatMapData')}}/{{$table}}/{{$key}}',
        'dataType': "json"
    }).success(function(data){
        heatmap.setData(data);
    });
    @endif
    @if(isset($settings->table) && isset($settings->key))
    $.ajax({
        'url': '{{action('\CityNexus\CityNexus\Http\ViewController@getHeatMapData')}}/{{$settings->table}}/{{$settings->key}}',
        'dataType': "json"
    }).success(function(data){
        heatmap.setData(data);
    });
    @endif
function refreshMap(table, key)
    {
        $.ajax({
            'url': '{{action('\CityNexus\CityNexus\Http\ViewController@getHeatMapData')}}/' + table + '/' + key,
            'dataType': "json"
        }).success(function(data){
            heatmap.setData(data);
        });
    }
    map.addLayer(heatmap);
    $("#intensity").ionRangeSlider({
        min: 1,
        max: 100,
        from: @if(isset($settings->intensity)) {{$settings->intensity}} @else 50 @endif
        }).on('change', function (ev) {
        var value = $('#intensity').val();
        heatmap.multiply(value / 50);
    });
    @if(isset($setting->intensity))
     heatmap.multiply({{$setting->intensity/50}});
    @endif
function saveReport() {
        var table_id = $('#h_dataset').val();
        var table_name = $('#table_name').val();
        var key = $('#datafield').val();
        var intensity = $('#intensity').val();
        var name = prompt('What name would you like to give this report view?', 'Unnamed Report');
        if(name != null)
        {
            $.ajax({
                url: "{{action('\CityNexus\CityNexus\Http\ViewController@postSaveView')}}",
                type: 'post',
                data: {
                    _token: "{{csrf_token()}}",
                    settings: {
                        type: 'Heat Map',
                        table_name: table_name,
                        table_id: table_id,
                        key: key,
                        intensity: intensity,
                    },
                    name: name
                }
            }).success(function (data) {
                Command: toastr["success"](name, "Report View Saved");
                $('#save-report-line').html( data );
            });
        }
    }
    function updateReport( id )
    {
        var table_id = $('#h_dataset').val();
        var key = $('#datafield').val();
        var table_name = $('#table_name').val();
        var intensity = $('#intensity').val();
        $.ajax({
            url: "{{action('\CityNexus\CityNexus\Http\ViewController@postSaveView')}}",
            type: 'post',
            data: {
                _token: "{{csrf_token()}}",
                settings: {
                    type: 'Heat Map',
                    table_name: table_name,
                    table_id: table_id,
                    key: key,
                    intensity: intensity,
                },
                id: id
            }
        }).success(function(){
            Command: toastr["success"](name, "Report View Updated");
        });
    }
</script>
@endpush