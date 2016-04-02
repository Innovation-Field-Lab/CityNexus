<?php
function printTitle ($title) {
    echo '<a name="' . strtolower($title) . '"></a>';
    echo '<h3><a href="#' . strtolower($title) . '">'.ucwords($title).':</a></h3>';
}
?>
        <!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="author" content="Ursudio" />
    <meta name="robots" content="noindex, nofollow, noarchive" />
    <meta name="viewport" content="width=820" />
    <link rel="stylesheet" href="//cdn.leafletjs.com/leaflet/v0.7.7/leaflet.css"></link>
    <link href='http://fonts.googleapis.com/css?family=Open+Sans|Fjalla+One' rel='stylesheet' type='text/css'>
    <link type="text/css" rel="stylesheet" href="/css/shCoreEclipse.css"/>
    <style>
        html {
            background:#F0F0F0;
            color:#555;
            font-family:arial, sans-serif; font-family: 'Open Sans', sans-serif;
        }
        h1,
        h2 {
            text-shadow: #FFF 1px 1px 0;
            font-family: 'Fjalla One', sans-serif;
        }
        h3 {
            color:#7CAD55;
        }
        h3:before {
            content:"#";
            color:#DDD;
            position:absolute;
            margin-left:-20px;
        }
        a {
            color:#7C9ED1;
            text-decoration:none;
            transition: all .3s;
            -moz-transition: all .3s;
            -webkit-transition: all .3s;
        }
        a:hover {
            color:#BDBD44;
        }
        #navigation a:before,
        #navigation a:after {
            margin-top:5px;
            transition: all .1s ease-out;
            -webkit-transition: all .1s ease-out;
            -moz-transition: all .1s ease-out;
            position: absolute;
            opacity:0;
            display:block;
            visibility:hidden;
        }
        #navigation a:before {
            content: '';
            border: 8px solid transparent;
            border-bottom-color:black;
            top:24px;
            left:35%;
        }
        #navigation a:after {
            content: attr(data-title);
            background: black;
            color: white;
            padding: 2px 7px;
            top: 40px;
            font-size:.8em;
            text-align:center;
            white-space:nowrap;
        }
        #navigation li.floatRight a:after {
            right:0;
        }
        #navigation a:hover:before,
        #navigation a:hover:after {
            margin-top:0;
            opacity: 1;
            visibility: visible;
        }
        #navigation ul {
            padding: 0 50px;
        }
        a[name] {
            position:absolute;
            margin-top:-40px;
        }
        body {
            margin:0;
        }
        .container {
            padding:15px 10%;
        }
        .container.first {
            padding:5px 0;
            position:fixed;
            z-index:9999;
            width:100%;
            background: #DDD;
        }
        .container.last {
            background:#222 url(images/dark_wall.png);
            min-height:185px;
        }
        ul {
            list-style:none;
            margin:0;
        }
        li {
            line-height:40px;
        }
        #navigation ul {
            list-style:none;
            height:29px;
            margin:0;
        }
        #navigation li {
            float:left;
            line-height:initial;
            position:relative;
        }
        #navigation li.floatRight {
            float:right;
        }
        li a {
            padding: 12px 20px;
            color:#222;
            transition: all .3s;
            -moz-transition: all .3s;
            -webkit-transition: all .3s;
        }
        li a:hover {
            background-color:rgba(255,255,255,.2);
        }
        li a img {
            vertical-align:middle;
        }
        #header {
            padding:100px 0 0;
            text-align:center;
        }
        #map {
            width:100%;
            height:500px;
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
    </style>
    <!--[if lte IE 8]>
    <link rel="stylesheet" href="leaflet/dist/leaflet.ie.css" />
    <![endif]-->
    <title>
        Web GL Heatmap Leaflet Plugin
    </title>
</head>
<body>
<div id="map"></div>
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
                [{{$score->lat}}, {{$score->long}}, {{$score->score/6}}],
            @endforeach
    ];

    heatmap.setData( dataPoints );
    heatmap.multiply( 2 );

    map.addLayer( heatmap );

    SyntaxHighlighter.all();

</script>

</body>
</html>