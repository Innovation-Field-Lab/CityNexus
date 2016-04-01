
@extends(config('citynexus.template'))

@section(config('citynexus.section'))

    <div id="mapid" style="width: 100%; height: 800px"></div>

@stop

@push('style')

<link rel="stylesheet" href="http://cdn.leafletjs.com/leaflet/v0.7.7/leaflet.css" />

@endpush

@push('js_footer')

<script src="http://cdn.leafletjs.com/leaflet/v0.7.7/leaflet.js"></script>
<script src="/js/PruneCluster.js"></script>
<script>
    var mymap = L.map('mapid').setView([{{env('MAP_LAT')}}, {{env('MAP_LONG')}}], {{env('MAP_ZOOM')}});
    L.tileLayer('https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token={accessToken}', {
        attribution: 'Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors, <a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="http://mapbox.com">Mapbox</a>',
        maxZoom: 18,
        id: 'seanalaback.piieb4dh',
        accessToken: 'pk.eyJ1Ijoic2VhbmFsYWJhY2siLCJhIjoiY2ltaTNpbWtrMDA0YnV0a2c3YjQxZ2YxYyJ9.w85abrrlR743J2MVtrROKw'
    }).addTo(mymap);

    var pruneCluster = new PruneClusterForLeaflet();

    @foreach($pins as $pin)
    var marker_{{$pin->id}} = new PruneCluster.Marker({{$pin->lat}}, {{$pin->long}});
    marker_{{$pin->id}}.data.popup = '<a href="/{{config('citynexus.root_directory')}}/property/{{$pin->id}}" target="_blank"><i class="glyphicon glyphicon-new-window"></i> {{ucwords($pin->full_address)}}</a>';
    pruneCluster.RegisterMarker(marker_{{$pin->id}});
    @endforeach

    mymap.addLayer(pruneCluster);
</script>
@endpush