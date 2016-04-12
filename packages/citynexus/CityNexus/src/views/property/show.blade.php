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
                        <li><a href="{{action('\CityNexus\CityNexus\Http\CitynexusController@getProperty', ['property_id' => $alias->id])}}" id="demerge-alias">
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
                            {{ucwords($property->full_address)}}
                        </a>
                        <a href="{{action('\CityNexus\CityNexus\Http\TablerController@getDemergeProperty', ['property_id' => $property->id])}}">
                            <i class="glyphicon glyphicon-trash" style="color:red"></i>
                        </a>)
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
                <div class="col-sm-4">

                    @if($property->lat != null && $property->long != null)
                        <div class="panel panel-default">
                                <div id="pano" style="height: 250px"></div>
                        </div>
                        <div class="panel panel-default">
                            <div id="map" style="height: 250px"></div>
                        </div>
                    @endif
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Property Tags
                        </div>
                        <div class="panel-body">
                            <div class="list-group" id="property_tags">
                                @forelse($property->tags as $tag)
                                    @include('citynexus::property._tag')
                                @empty
                                    <div class="alert alert-info" id="no-tags"> No tags currently associated with property</div>
                                @endforelse
                                <div class="hidden" id="pending"><i class="glyphicon glyphicon-refresh"></i></div>
                            </div>
                        </div>
                        <div class="panel-footer">
                            <div id="new-tag-input">
                                <input class="form-control typeahead" type="text" id="new-tag" placeholder="Add new tag">
                            </div>
                        </div>
                    </div>
                </div>

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

<script src="https://cdnjs.cloudflare.com/ajax/libs/typeahead.js/0.11.1/typeahead.jquery.js"></script>

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

{{--add tags--}}

<script>
    var substringMatcher = function(strs) {
        return function findMatches(q, cb) {
            var matches, substringRegex;

            // an array that will be populated with substring matches
            matches = [];

            // regex used to determine if a string contains the substring `q`
            substrRegex = new RegExp(q, 'i');

            // iterate through the pool of strings and for any string that
            // contains the substring `q`, add it to the `matches` array
            $.each(strs, function(i, str) {
                if (substrRegex.test(str)) {
                    matches.push(str);
                }
            });

            cb(matches);
        };
    };

    var tags = {!! json_encode($tags) !!};
    $('#new-tag-input .typeahead').typeahead({
                hint: true,
                highlight: true,
                minLength: 1
            },
            {
                name: 'states',
                source: substringMatcher(tags)
            });

    $("#new-tag").bind("keypress", {}, addTag);
    function addTag(e) {
        var code = (e.keyCode ? e.keyCode : e.which);
        if (code == 13) { //Enter keycode
            e.preventDefault();

            var tag = $('#new-tag').val();
            $('#new-tag').val('');
            $('#no-tags').addClass('hidden');
            $('#pending').removeClass('hidden');
            $.ajax({
                url: "/{{config('citynexus.root_directory')}}/associate-tag",
                type: 'post',
                data: {
                    _token: "{{csrf_token()}}",
                    property_id: {{$property->id}},
                    tag: tag
                }
            }).success( function( data ) {
                $("#pending").addClass('hidden');
                $('#property_tags').append(data);
            }
        );
        }
    };


</script>

{{--Delete Tag--}}

<script>
    function confirmDelete(id)
    {
        $('#delete-tag-' + id).addClass('btn-danger');
        $('#delete-tag-' + id).attr('onclick', 'removeTag(' + id +')');
    }

    function removeTag(id)
    {
        $('#tag-' + id).addClass('hidden');
        $.ajax({
            url: "/{{config('citynexus.root_directory')}}/remove-tag",
            type: "post",
            data: {
                _token: "{{csrf_token()}}",
                property_id: {{$property->id}},
                tag_id: id
            }
        })
    }
</script>

@endpush

@push('style')
<style>
    .typeahead,
    .tt-query,
    .tt-hint {
        width: 100%;
        padding: 8px 8px;
        border: 2px solid #ccc;
        -webkit-border-radius: 8px;
        -moz-border-radius: 8px;
        border-radius: 8px;
        outline: none;
    }

    .typeahead {
        background-color: #fff;
    }

    .typeahead:focus {
        border: 2px solid #0097cf;
    }

    .tt-query {
        -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
        -moz-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
        box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
    }

    .tt-hint {
        color: #999
    }

    .tt-menu {
        width: 100px;
        margin: 12px 0;
        background-color: #fff;
        border: 1px solid #ccc;
        border: 1px solid rgba(0, 0, 0, 0.2);
        -webkit-border-radius: 8px;
        -moz-border-radius: 8px;
        border-radius: 8px;
        -webkit-box-shadow: 0 5px 10px rgba(0,0,0,.2);
        -moz-box-shadow: 0 5px 10px rgba(0,0,0,.2);
        box-shadow: 0 5px 10px rgba(0,0,0,.2);
    }

    .tt-suggestion {
        padding: 3px 20px;

    }

    .tt-suggestion:hover {
        cursor: pointer;
        color: #fff;
        background-color: #0097cf;
    }

    .tt-suggestion.tt-cursor {
        color: #fff;
        background-color: #0097cf;

    }

    .tt-suggestion p {
        margin: 0;
    }

    .gist {
        font-size: 14px;
    }

</style>

@endpush

