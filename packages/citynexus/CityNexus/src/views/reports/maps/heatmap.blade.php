
@extends(config('citynexus.template'))

@section(config('citynexus.section'))
    <div class="panel panel-default" id="score-picker">
        <div class="panel-heading">
            Score
        </div>
        <div class="panel-body" >
            <select name="scores" id="scores" class="form-control">
                @foreach($scores as $i)
                    <option value="{{$i->id}}" @if($i->id == $rs->id) selected @endif>{{$i->name}}</option>
                @endforeach
            </select>
            </br>
            <div class="btn btn-block btn-primary" onclick="refresh()"> Refresh </div>
        </div>
    </div>

    <div id="map"></div>

@push('style')
<link rel="stylesheet" href="//cdn.leafletjs.com/leaflet/v0.7.7/leaflet.css"></link>
<link href='http://fonts.googleapis.com/css?family=Open+Sans|Fjalla+One' rel='stylesheet' type='text/css'>
<link type="text/css" rel="stylesheet" href="/css/shCoreEclipse.css"/>
<!--[if lte IE 8]>
<link rel="stylesheet" href="/css/leaflet.ie.css" /> -->

<style>
    #map {
        width:100%;
        height:100%;
        margin:0 auto 40px;
    }
    #map a {
        color:initial;
    }
    canvas {
        opacity: 1
    }
    .syntaxhighlighter {
        overflow-y:hidden !important;
        padding: 10px 0;
    }

    #score-picker
    {
        width: 250px;
        position: fixed;
        right: 50px;
        height: 200px;
        margin-top: 20px;
        z-index: 50;
    }
</style>

@stop

@push('js_footer')
<script src="//cdn.leafletjs.com/leaflet/v0.7.7/leaflet.js"></script>
<script type="text/javascript" src="/js/shCore.js"></script>
<script type="text/javascript" src="/js/shBrushJScript.js"></script>

<script type="text/javascript" src="/js/webgl-heatmap.js"></script>
<script type="text/javascript" src="/js/webgl-heatmap-leaflet.js"></script>
<script type="text/javascript">
    var map = L.map('map', {
        center : [{{config('citynexus.map_lat')}}, {{config('citynexus.map_long')}}],
        zoom: {{config('citynexus.map_zoom')}}
    });

    L.tileLayer('http://otile{s}.mqcdn.com/tiles/1.0.0/map/{z}/{x}/{y}.jpg', {
        subdomains: '1234'
    }).addTo( map );
    map.attributionControl.addAttribution('Tiles Courtesy of <a href="http://www.mapquest.com/" target="_blank">MapQuest</a> <img src="http://developer.mapquest.com/content/osm/mq_logo.png" />');
    map.attributionControl.addAttribution(' © <a href="http://www.openstreetmap.org/">OpenStreetMap</a> contributors');
    map.scrollWheelZoom.disable();

    L.control.scale().addTo(map);

    //custom size for this example, and autoresize because map style has a percentage width
    var heatmap = L.webGLHeatmap({
        size: 50,
        units : 'px',
        alphaRange: 0.4
    });

    var dataPoints = [
            @foreach($data as $score)
                [{{$score->lat}}, {{$score->long}}, {{$score->score/$max}}],
        @endforeach
];

    heatmap.setData( dataPoints );
    heatmap.multiply(.5);

    map.addLayer( heatmap );

    SyntaxHighlighter.all();

    function refresh()
    {
        var score = $("#scores").val();
        var url = "/{{config('citynexus.root_directory')}}/risk-score/heat-map?score_id=" + score;
        window.location.replace(url);
    }

</script>
@stop
