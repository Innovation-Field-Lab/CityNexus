
<?php
        $pagename = "Heat Map";
        $section = "reports";
?>

@extends(config('citynexus.template'))

@section(config('citynexus.section'))

    <div class="col-md-12">
        <div class="card-box">
            <div class="dropdown pull-right">
                <a href="#" class="dropdown-toggle card-drop" data-toggle="dropdown"
                   aria-expanded="false">
                    <i class="zmdi zmdi-more-vert"></i>
                </a>
                <ul class="dropdown-menu" role="menu">
                    <li><a href="javascript:void(0);" class="map-bar-toggle">Open Map Settings</a></li>
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
<link rel="stylesheet" href="/css/leaflet.css"/>
    <link rel="stylesheet" href="/css/slider.css"/>
<link href='http://fonts.googleapis.com/css?family=Open+Sans|Fjalla+One' rel='stylesheet' type='text/css'>
<link type="text/css" rel="stylesheet" href="/css/shCoreEclipse.css"/>
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

    <link href="/plugins/ion-rangeslider/ion.rangeSlider.css" rel="stylesheet" type="text/css"/>
    <link href="/plugins/ion-rangeslider/ion.rangeSlider.skinFlat.css" rel="stylesheet" type="text/css"/>

@endpush

@push('js_footer')
<script src="/js/leaflet.js"></script>
<script type="text/javascript" src="/js/shCore.js"></script>
<script type="text/javascript" src="/js/shBrushJScript.js"></script>

<script type="text/javascript" src="/js/webgl-heatmap.js"></script>
<script type="text/javascript" src="/js/webgl-heatmap-leaflet.js"></script>
<script src="/plugins/ion-rangeslider/ion.rangeSlider.min.js"></script>
<script type="text/javascript">

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
            'url': '{{action('\CityNexus\CityNexus\Http\ReportsController@getHeatMapData')}}/{{$table}}/{{$key}}',
            'dataType': "json"
        }).success(function(data){
            heatmap.setData(data);
        });

        @endif

        function refreshMap(table, key)
        {
            $.ajax({
                'url': '{{action('\CityNexus\CityNexus\Http\ReportsController@getHeatMapData')}}/' + table + '/' + key,
                'dataType': "json"
            }).success(function(data){
                heatmap.setData(data);
            });
        }

        map.addLayer(heatmap);

        $("#intensity").ionRangeSlider({
            min: 1,
            max: 100,
            from: 50
        }).on('change', function (ev) {
            var value = $('#intensity').val();
            heatmap.multiply(value / 50);
        });

</script>
@endpush
