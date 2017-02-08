<?php
$pagename = "Dot Map";
$section = "reports";
?>

@extends(config('citynexus.template'))

@section(config('citynexus.section'))
    <div class="col-md-12" id="map-wrapper">
        <div class="card-box">
            <h4 class="header-title m-t-0" id="map-name"></h4>
            <div id="mapid">
                <div id="map-settings-toggle">
                    <i class="fa fa-cog fa-3x" id="settings_cog"></i>
                </div>
            </div>
        </div>
    </div><!-- end col -->
    <div class="col-md-3 hidden" id="map-settings">
        <div class="card-box">
            <i class="fa fa-angle-double-right fa-1x" style="cursor: pointer" id="hide-settings"></i>
            <br><br>
            <h4 class="header-title m-t-0" id="map-name">Choose Data Point</h4>

            <div id="datasets">
                <ul>
                    <li data-jstree='{"opened":false}'> Scores
                        <ul>
                        @foreach($scores as $score)
                            <li data-jstree='{"type":"score"}' onclick="loadScore({{$score->id}})">{{$score->name}} </li>
                        @endforeach
                        </ul>
                    </li>
                    @foreach($datasets as $dataset)
                        <li data-jstree='{"opened":false}'>{{$dataset->table_title}}
                            <ul>
                                <li data-jstree='{"type": "datacount"}' onclick="loadDataset({{$dataset->id}})">Count of Records</li>
                                @foreach($dataset->schema as $field)
                                    <li data-jstree='{"type":"{{$field->type}}"}' onclick="loadDatasetPoint({{$dataset->id}}, '{{$field->key}}');">{{$field->name}}</li>
                                @endforeach
                            </ul>
                        </li>
                    @endforeach
                </ul>
            </div>

        </div>
        <div id="layerCards"></div>
    </div>

@stop

{{--@push('sidebar')--}}
{{--@include('citynexus::reports.includes.heatmap._map_setting')--}}
{{--@endpush--}}

@push('style')

<link href='http://fonts.googleapis.com/css?family=Open+Sans|Fjalla+One' rel='stylesheet' type='text/css'>
<link type="text/css" rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/SyntaxHighlighter/3.0.83/styles/shCoreEclipse.min.css"/>

<link href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.0.2/leaflet.css" rel="stylesheet" type="text/css" />
<link href='https://api.mapbox.com/mapbox.js/plugins/leaflet-fullscreen/v1.0.1/leaflet.fullscreen.css' rel='stylesheet' />
<link href="/vendor/citynexus/plugins/jstree/style.css" rel="stylesheet" type="text/css" />


<!--[if lte IE 8]>
<link rel="stylesheet" href="/css/leaflet.ie.css" /> -->

<style>
    #mapid {
        width:100%;
        margin:0 auto 0px;
    }
    #map a {
        color:initial;
    }
    canvas {
        opacity: 1
    }
    #map-settings-toggle {
        position: relative;
        float: right;
        padding: 5px;
        right: 10px;
        top: 10px;
        background-color: white;
        z-index: 1000;
        border-radius: 5px;
        cursor: pointer;
    }

    #map-settings-toggle:hover {
        background-color: lightgray;
    }

    #datasets {
        max-height: 300px;
        overflow: scroll;
    }

</style>

@endpush

@push('js_footer')
<script src="https://cdnjs.cloudflare.com/ajax/libs/typeahead.js/0.11.1/typeahead.jquery.js"></script>

    <script src="https://unpkg.com/leaflet@1.0.2/dist/leaflet.js"></script>
    <script src="http://d3js.org/d3.v3.min.js" charset="utf-8"></script>
    <script src='https://api.mapbox.com/mapbox.js/plugins/leaflet-fullscreen/v1.0.1/Leaflet.fullscreen.min.js'></script>
    <script src="/vendor/citynexus/plugins/jstree/jstree.min.js"></script>



    <script>
        function updateHeight(h)
        {
            document.getElementById('mapid').style.height = (h-225) + 'px';
        }

        updateHeight($(window).height());

        $(window).resize(function(){
            updateHeight($(window).height());
        });

        var mymap = L.map('mapid', {
            fullscreenControl: true,
        }).setView([{{env('MAP_LAT')}}, {{env('MAP_LONG')}}], {{env('MAP_ZOOM')}});

        L.tileLayer('https://api.mapbox.com/styles/v1/seanalaback/ciwtk4ush002o2qrxo43r8o13/tiles/256/{z}/{x}/{y}?access_token={accessToken}', {
            attribution: 'Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors, <a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="http://mapbox.com">Mapbox</a>',
            maxZoom: 20,
            accessToken: "{{env('MAPBOX_TOKEN')}}"
        }).addTo(mymap);

        var layers = new Array();

        var loadDatasetPoint = function (dataset_id, key) {
            $("#settings_cog").addClass('fa-spin');

            $.ajax({
                type: 'post',
                url: '/citynexus/reports/views/dot-map',
                data: {
                    _token: "{{csrf_token()}}",
                    type: 'datapoint',
                    key: key,
                    dataset_id: dataset_id

                },
                success: function(data) {
                    reloadMap(data['points'], data['title'], data['max'], data['handle'])
                },
                error: function(data){
                    $("#settings_cog").removeClass('fa-spin');
                    alert('Oh oh, something went wrong.');
                }
            });

        };

        var loadDataset = function (dataset_id) {
            $("#settings_cog").addClass('fa-spin');

            $.ajax({
                type: 'post',
                url: '/citynexus/reports/views/dot-map',
                data: {
                    _token: "{{csrf_token()}}",
                    type: 'dataset',
                    dataset_id: dataset_id

                },
                success: function(data) {
                    reloadMap(data['points'], data['title'], data['max'], data['handle'])
                },
                error: function(data){
                    $("#settings_cog").removeClass('fa-spin');
                    alert('Oh oh, something went wrong.');
                }
            });

        };

        var loadScore = function (id) {
            $("#settings_cog").addClass('fa-spin');

            $.ajax({
                type: 'post',
                url: '/citynexus/reports/views/dot-map',
                data: {
                    _token: "{{csrf_token()}}",
                    type: 'score',
                    id: id,
                },
                success: function(data) {
                    reloadMap(data['points'], data['title'], data['max'], data['handle'])
                },
                error: function(data){
                    $("#settings_cog").removeClass('fa-spin');
                    alert('Oh oh, something went wrong.');
                }
            });

        };


        var colors = {
            0: {
                layer: null,
                color:'#c93635'
            },
            1: {
                layer: null,
                color: '#003f5e'
            },
            2: {
                layer: null,
                color: '#35c980'
            },
            3: {
                layer: null,
                color: '#5e003f'
            },
            4: {
                layer: null,
                color: '#35c8c9'
            },
            5: {
                layer: null,
                color: '#357ec9'
            }
        };

        var newColor = function(layer)
        {

            for (var i=0; i < Object.keys(colors).length;  ++i)
            {
                if (colors[i]['layer'] == null)
                {
                    colors[i].layer = layer;
                    return colors[i].color;
                }
            }

        };

        var reloadMap = function(markers, title, max, handle)
        {
            layers[handle] = L.layerGroup();

            var color = newColor(handle);

            for (var i=0; i < markers.length;  ++i)
            {
                layers[handle].addLayer( new L.circleMarker( [markers[i].lat, markers[i].lng], {
                            radius: 4,
                            stroke: false,
                            color: 'black',
                            opacity: 1,
                            fill: true,
                            fillColor: color,
                            fillOpacity: (markers[i].value/max) + .1
                        } ).bindPopup( '<b>Value: ' + markers[i].value + '</b><br><a href="' + markers[i].url + '" target="_blank">' + markers[i].name + '</a>' )
                );
            }

            layers[handle].addTo(mymap);
            createLayerBox(handle, color, title);
            $("#settings_cog").removeClass('fa-spin');
        };

        @if(isset($_GET['is_score']))
        loadScore({{$_GET['score_id']}});
        @elseif(isset($_GET['is_dataset']))
        loadDataset({{$_GET['dataset_id']}});
        @elseif(isset($_GET['is_datapoint']))
        loadDatasetPoint({{$_GET['dataset_id']}}, '{{$_GET['key']}}');
        @else
        $('#map-wrapper').removeClass('col-md-12').addClass('col-md-9');
        $('#map-settings').removeClass('hidden');
        @endif

        $('#hide-settings').click(function(){
            $('#map-wrapper').addClass('col-md-12').removeClass('col-md-9');
            $('#map-settings').addClass('hidden');
        });

        $('#settings_cog').click(function(){
            $('#map-wrapper').removeClass('col-md-12').addClass('col-md-9');
            $('#map-settings').removeClass('hidden');
        });

        var createLayerBox = function(layer, color, name)
        {
            var box = '<div class="card-box" id="layer_' + layer + '"><span class="fa fa-square" style="color: ' + color + '"></span> <b>' + name + '</b><span class="fa fa-trash pull-right" style="cursor: pointer" onclick="removeLayer(\'' + layer + '\')"></span></div>';
            $('#layerCards').append(box);
        };

        function removeLayer(layer) {
            layer = layer.trim();
            layers[layer].clearLayers();
            $('#layer_' + layer).remove();

            for (var i=0; i < Object.keys(colors).length;  ++i)
            {
                if (colors[i]['layer'] == layer)
                {
                    colors[i].layer = null;
                    break;
                }
            }
        };

    </script>
{{--<script>--}}
{{--function saveReport() {--}}
        {{--var table_id = $('#h_dataset').val();--}}
        {{--var table_name = $('#table_name').val();--}}
        {{--var key = $('#datafield').val();--}}
        {{--var intensity = $('#intensity').val();--}}
        {{--var name = prompt('What name would you like to give this report view?', 'Unnamed Report');--}}
        {{--if(name != null)--}}
        {{--{--}}
            {{--$.ajax({--}}
                {{--url: "{{action('\CityNexus\CityNexus\Http\ViewController@postSaveView')}}",--}}
                {{--type: 'post',--}}
                {{--data: {--}}
                    {{--_token: "{{csrf_token()}}",--}}
                    {{--settings: {--}}
                        {{--type: 'Dot Map',--}}
                        {{--table_name: table_name,--}}
                        {{--table_id: table_id,--}}
                        {{--key: key,--}}
                        {{--intensity: intensity,--}}
                    {{--},--}}
                    {{--name: name--}}
                {{--}--}}
            {{--}).success(function (data) {--}}
                {{--Command: toastr["success"](name, "Report View Saved");--}}
                {{--$('#save-report-line').html( data );--}}
            {{--});--}}
        {{--}--}}
    {{--}--}}
{{--function updateReport( id )--}}
    {{--{--}}
        {{--var table_id = $('#h_dataset').val();--}}
        {{--var key = $('#datafield').val();--}}
        {{--var table_name = $('#table_name').val();--}}
        {{--var intensity = $('#intensity').val();--}}
        {{--$.ajax({--}}
            {{--url: "{{action('\CityNexus\CityNexus\Http\ViewController@postSaveView')}}",--}}
            {{--type: 'post',--}}
            {{--data: {--}}
                {{--_token: "{{csrf_token()}}",--}}
                {{--settings: {--}}
                    {{--type: 'Heat Map',--}}
                    {{--table_name: table_name,--}}
                    {{--table_id: table_id,--}}
                    {{--key: key,--}}
                    {{--intensity: intensity,--}}
                {{--},--}}
                {{--id: id--}}
            {{--}--}}
        {{--}).success(function(){--}}
        {{--Command: toastr["success"](name, "Report View Updated");--}}
    {{--});--}}
{{--}--}}

    {{--$('#map-settings-toggle').click(function(){--}}
        {{--$('#map-wrapper').removeClass('col-md-12').addClass('col-md-9');--}}
        {{--$('#map-settings').removeClass('hidden');--}}

    {{--});--}}
{{--</script>--}}

<script>
    $('#datasets').jstree({
        'core': {
            'themes': {
                'responsive': false
            }
        },
        'types': {
            'default': {
                'icon': 'fa fa-folder'
            },
            'score': {
                'icon': 'zmdi zmdi-pin'
            },
            'integer': {
                'icon': 'zmdi zmdi-n-1-square'
            },
            'string': {
                'icon': 'ti-text'
            },
            'datetime': {
                'icon': 'ti-calendar'
            },
            'float': {
                'icon': 'zmdi zmdi-n-1-square'
            },
            'boolean': {
                'icon': 'zmdi zmdi-file'
            },
            'datacount': {
                'icon': 'zmdi zmdi-collection-item'
            }
        },
        'plugins': ['types']
    });

</script>
@endpush