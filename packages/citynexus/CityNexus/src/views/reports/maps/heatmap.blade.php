
@extends('app')

@section('content')

    <div class="panel panel-default">
        <div class="panel-heading">
            <span class="panel-title">{{$title}}</span>
        </div>
        <div class="panel-body" >
            <div id="map" style="height: 700px"></div>
        </div>
    </div>

    @stop

@push('javascript')

<script>
    var map, heatmap;

    function initMap() {
        map = new google.maps.Map(document.getElementById('map'), {
            zoom: 14,
            center: {lat: 42.39572, lng: -71.035},
            mapTypeId: google.maps.MapTypeId.MAP
        });

        heatmap = new google.maps.visualization.HeatmapLayer({
            data: getPoints(),
            map: map
        });
    }
    // Heatmap data: 500 Points
    function getPoints() {
        return [ @foreach($data as $item)
                @while($item['score'] != 0)
                    new google.maps.LatLng({{$item['lat']}}, {{$item['long']}}),
                        <?php $item['score']--; ?>
                    @endwhile
                @endforeach
                ];
    }
</script>

<script async defer
        src="https://maps.googleapis.com/maps/api/js?signed_in=true&libraries=visualization&callback=initMap">
</script>
@endpush