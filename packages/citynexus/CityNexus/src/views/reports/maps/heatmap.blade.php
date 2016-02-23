
@extends(config('citynexus.template'))

@section(config('citynexus.section'))
    <div class="row">
        <div class="col-md-10">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <span class="panel-title">{{$rs->name}}</span>
                </div>
                <div class="panel-body" >
                    <div id="map" style="height: 700px"></div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="panel panel-default">
                <div class="panel-heading">
                    Score
                </div>
                <div class="panel-body">
                    <select name="scores" id="scores" class="form-control">
                        @foreach($scores as $i)
                            <option value="{{$i->id}}" @if($i->id == $rs->id) selected @endif>{{$i->name}}</option>
                        @endforeach
                    </select>
                    </br>
                    <div class="btn btn-block btn-primary" onclick="refresh()"> Refresh </div>
                </div>
            </div>

        </div>
    </div>



    @stop

@push('js_footer')

<script>
    var map, heatmap;

    function initMap() {
        map = new google.maps.Map(document.getElementById('map'), {
            zoom: 15,
            center: {lat: 42.39572, lng: -71.035},
            mapTypeId: google.maps.MapTypeId.MAP
        });

        var styles = [
            {
                stylers: [
                    { hue: "#00ffe6" },
                    { saturation: -40 }
                ]
            },{
                featureType: "road",
                elementType: "geometry",
                stylers: [
                    { lightness: 120 },
                    { visibility: "simplified" }
                ]
            },{
                featureType: "road",
                elementType: "labels",
                stylers: [
                    { visibility: "on" }
                ]
            }
        ];

        map.setOptions({styles: styles});

        heatmap = new google.maps.visualization.HeatmapLayer({
            data: getPoints(),
            map: map
        });
    }
    // Heatmap data
    function getPoints() {
        return [@foreach($data as $item)
               @if($item->lat != null)
                    {
                location: new google.maps.LatLng({{$item->lat}}, {{$item->long}}),
                weight: Math.pow({{$item->score}})

    },
            @endif
        @endforeach  ];
    }

    function refresh()
    {
        var score = $("#scores").val();
        var url = "/{{config('citynexus.root_directory')}}/risk-score/heat-map?score_id=" + score;
        window.location.replace(url);
    }
</script>

<script async defer
        src="https://maps.googleapis.com/maps/api/js?signed_in=true&libraries=visualization&callback=initMap">
</script>
@endpush