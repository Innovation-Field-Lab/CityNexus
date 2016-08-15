<?php
$pagename = "Pin Map - " . $tag->tag;
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

@push('style')

<link rel="stylesheet" href="/vendor/citynexus/css/leaflet.css" />
<link rel="stylesheet" href="/vendor/citynexus/css/prunecluster.css" />

<style>

</style>

@endpush

@push('js_footer')

<script src="/vendor/citynexus/js/leaflet.js"></script>
<script src="/vendor/citynexus/js/PruneCluster.js"></script>
<script>
    var mymap = L.map('mapid').setView([{{env('MAP_LAT')}}, {{env('MAP_LONG')}}], {{env('MAP_ZOOM')}});
    L.tileLayer('https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token={accessToken}', {
        attribution: 'Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors, <a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="http://mapbox.com">Mapbox</a>',
        maxZoom: 22,
        id: 'seanalaback.piieb4dh',
        accessToken: 'pk.eyJ1Ijoic2VhbmFsYWJhY2siLCJhIjoiY2ltaTNpbWtrMDA0YnV0a2c3YjQxZ2YxYyJ9.w85abrrlR743J2MVtrROKw'
    }).addTo(mymap);

    var pruneCluster = new PruneClusterForLeaflet();

    @foreach($pins as $pin)
        @if($pin->lat != null && $pin->long != null)
            var marker = new PruneCluster.Marker({{$pin->lat}}, {{$pin->long}});
            marker.data.name = '{{$pin->full_address}}';
            @if($pin->score != null)
            marker.category = {{$pin->score}};
            @endif
            marker.data.popup = '<a href="{{action('\CityNexus\CityNexus\Http\PropertyController@getShow', ['id' => $pin->id])}}" target="_blank"><i class="glyphicon glyphicon-new-window"></i> {{ucwords($pin->full_address)}}</a>@if(null != $pin->score) <br><b>Score: {{$pin->score}} </b> @endif';
            pruneCluster.RegisterMarker(marker);
        @endif
    @endforeach

    mymap.addLayer(pruneCluster);

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
                        type: 'Pin Map',
                        tag_id: {{$tag->id}}
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
                    type: 'Pin Map',
                    tag_id: {{$tag->id}}
                    },
                id: id
            }
        }).success(function(){
            Command: toastr["success"](name, "Report View Updated");
        });
    }
</script>
@endpush