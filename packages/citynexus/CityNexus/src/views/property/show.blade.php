@extends(config('citynexus.template'))

@section(config('citynexus.section'))

    <div class="panel panel-default">
        <div class="panel-heading">

            <div class="dropdown pull-right">
                <div class="dropdown-toggle" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true" style="cursor: pointer">
                    <i class="glyphicon glyphicon-chevron-down"></i>
                </div>
                <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                    <li><a href="{{action('\CityNexus\CityNexus\Http\TablerController@getMergeRecords')}}/{{$property->id}}">Merge Property</a></li>
                    <li><a href="#">Add Record</a></li>
                </ul>
            </div>
            <div class="panel-title">
                {{ucwords($property->address())}}
                @if($property->aliases->count() > 0)
                <span class="dropdown">
                    <span class="dropdown-toggle" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true" style="cursor: pointer">
                        <i class="glyphicon glyphicon-duplicate"></i>
                    </span>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                        <li><a href="#">Aliases:</a></li>
                        @foreach($property->aliases as $alias)
                        <li><a href="{{action('\CityNexus\CityNexus\Http\CitynexusController@getProperty', ['property_id' => $alias->id])}}">
                                {{ucwords($alias->full_address)}}
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </span>
                @endif

                @if($property->aliasOf != null)
                    <small>(Alias of
                        <a href="{{action('\CityNexus\CityNexus\Http\CitynexusController@getProperty', ['property_id' => $property->aliasOf->id])}}">
                            {{ucwords($property->full_address)}})
                        </a>
                    </small>
                @endif
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
                                            <a class="glyphicon glyphicon-cog pull-right" href="/{{config('citynexus.tabler_root')}}/edit-table/{{$key}}"></a>

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
                            <div class="list-group" id="notes">
                                @forelse($property->notes as $note)
                                    @include('citynexus::property._note')
                                @empty
                                    <div class="list-group-item alert alert-info" id="no-notes">
                                        No notes for this property.
                                    </div>
                                @endforelse
                            </div>
                        </div>
                        <div class="panel-body">
                            <div class="form">
                                <textarea name="note" id="note" cols="30" rows="5" class="form-control"></textarea>
                                <br/>
                                <button class="btn btn-primary pull-right" onclick="saveNote()">Save Note</button>
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
    function saveNote()
    {
        var note = $('#note').val();

        $.ajax({
            url: "{{action('\CityNexus\CityNexus\Http\NoteController@postStore')}}",
            type: "Post",
            data: {
                _token: "{{csrf_token()}}",
                note: note,
                property_id: {{$property->id}},
            }
        }).success(function( data )
        {
            $('#notes').prepend( data );
            $('#note').val( null );
            $('#no-notes').addClass('hidden');
        })
    }

    function deleteNote( id  )
    {
        $.ajax({
            url: "{{action('\CityNexus\CityNexus\Http\NoteController@getDelete')}}/" + id,
            type: 'GET'
        }).success( function() {
            $("#note-" + id).addClass('hidden');
        })
    }
</script>

<script>
    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
    })
</script>

@endpush

