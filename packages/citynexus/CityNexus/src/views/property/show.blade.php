@extends(config('citynexus.template'))

@section(config('citynexus.section'))

    <div class="panel panel-default">
        <div class="panel-heading">

            <div class="dropdown pull-right">
                <div class="dropdown-toggle" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true" style="cursor: pointer">
                    <i class="glyphicon glyphicon-chevron-down"></i>
                </div>
                <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                    <li><a href="#">Merge Property</a></li>
                    <li><a href="#">Add Record</a></li>
                </ul>
            </div>
            <div class="panel-title">
                {{ucwords($property->address())}}
            </div>
        </div>
        <div class="panel-body">
                <div class="col-sm-8">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Property Information
                        </div>
                        @include('citynexus::property._profile_panel')
                    </div>

                    <div class="panel panel-default">
                        <div class="panel-body">
                            <div class="panel-group " id="accordion" role="tablist" aria-multiselectable="true">
                                @foreach($datasets as $key => $dataset)
                                <div class="panel panel-default ">
                                    <div class="panel-heading " role="tab" id="{{preg_replace('/\s+/', '_', $key)}}_heading">
                                        <h4 class="panel-title">
                                            <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#{{preg_replace('/\s+/', '_', $key)}}_detail" aria-expanded="false" aria-controls="collapseTwo">
                                                {{$tables->find($key)->table_title}}
                                            </a>
                                            <a class="glyphicon glyphicon-cog pull-right" href="/{{config('tabler.root_directory')}}/edit-table?table_id={{$key}}"></a>

                                        </h4>
                                    </div>
                                    <div id="{{preg_replace('/\s+/', '_', $key)}}_detail" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo">
                                        @include('citynexus::property._data_panel')
                                    </div>
                                </div>
                                @endforeach


                            </div>
                        </div>
                    </div>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <span class="panel-title">Notes</span>
                        </div>
                        <div class="panel-body">
                            <div class="form">
                                <textarea name="note" id="note" cols="30" rows="5" class="form-control"></textarea>
                                <br/>
                                <button class="btn btn-primary pull-right" onclick="saveNote()">Save Note</button>
                            </div>
                        </div>
                        <div class="panel-body">
                            <div class="list-group" id="notes">
                                {{--@forelse($notes as $note)--}}
                                    {{--@include('property._note')--}}
                                {{--@empty--}}
                                    {{--<div class="list-group-item alert alert-info">--}}
                                        {{--No notes for this property.--}}
                                    {{--</div>--}}
                                {{--@endforelse--}}
                                    {{--{!! $notes->render() !!}--}}
                            </div>
                        </div>
                    </div>
                </div>
                @if($property->lat != null && $property->long != null)
                <div class="col-sm-4">
                    <div class="panel panel-default">
                            <div id="pano" style="height: 250px"></div>
                    </div>
                    <div class="panel panel-default">
                        <div id="map" style="height: 250px"></div>
                    </div>
                </div>
                @endif
            </div>
            <div class="panel-body">
                <div class="col-sm-12">

                </div>
            </div>
        </div>
    </div>

@stop

@push('style')

    <style>
        .dataset {
           overflow: auto;
            overflow-y: hidden;
        }
    </style>

@endpush

@push('js_footer')

<script>

    function initialize() {

        var point = {lat: {{$property->lat}}, lng:{{$property->long}} };
        var map = new google.maps.Map(document.getElementById('map'), {
            center: point,
            zoom: 16
        });
        var panorama = new google.maps.StreetViewPanorama(
                document.getElementById('pano'), {
                    position: point,
                });
        map.setStreetView(panorama);
    }

</script>
<script async defer
        src="{{'https://maps.googleapis.com/maps/api/js?key=' . env('GMAPI_KEY') . '&signed_in=true&callback=initialize'}}">
</script>
<script>
    {{--function saveNote()--}}
    {{--{--}}
        {{--$.ajax({--}}
            {{--url: '/note/',--}}
            {{--type: 'Post',--}}
            {{--data: {--}}
                {{--_token: "{{csrf_token()}}",--}}
                {{--note: {--}}
                    {{--note: $('#note').val(),--}}
                    {{--property_id: {{$property->id}},--}}
                {{--}--}}
            {{--}--}}
        {{--}).success(function( data )--}}
        {{--{--}}
            {{--$('#notes').prepend( data );--}}
            {{--$('#note').val( null );--}}
        {{--})--}}
    {{--}--}}
</script>

<script>
    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
    })
</script>

@endpush

