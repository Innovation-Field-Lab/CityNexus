<?php
$pagename = ucwords($property->address());
if($property->aliases->count() > 0)
    { $pagename .=
    '<span class="dropdown">
                    <span class="dropdown-toggle" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true" style="cursor: pointer">
                        <i class="glyphicon glyphicon-duplicate"></i>
                    </span>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                        <li><a href="#">Aliases:</a></li>';
                        foreach($property->aliases as $alias)
                            {
                            $pagename .=
                            '<li><a href="' . action('\CityNexus\CityNexus\Http\PropertyController@getShow', ['property_id' => $alias->id]) . '" id="demerge-alias">
                                    ' . ucwords($alias->full_address) . '
                                </a>
                            </li>'; }

                    $pagename .= '</ul>
                </span>';
}

if($property->aliasOf != null)
    {

    $pagename .=
    '<small>(Alias of
        <a href="' . action('\CityNexus\CityNexus\Http\PropertyController@getShow', ['property_id' => $property->aliasOf->id]) . '">'
            . ucwords($property->full_address) . '
        </a>
        <a href="'  . action('\CityNexus\CityNexus\Http\TablerController@getDemergeProperty', ['property_id' => $property->id]) . '">
            <i class="glyphicon glyphicon-trash" style="color:red"></i>
        </a>)
    </small>';
}
$section = 'properties';
?>

@extends(config('citynexus.template'))

@section(config('citynexus.section'))

    <div class="card-box">
            <div class="dropdown pull-right">
                <div class="dropdown-toggle" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true" style="cursor: pointer">
                    <i class="zmdi zmdi-more-vert"></i>
                </div>
                <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                    <li><a href="{{action('\CityNexus\CityNexus\Http\TablerController@getMergeRecords')}}/{{$property->id}}">Merge Property</a></li>
                    <li><a href="#">Add Record</a></li>
                </ul>
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
                                @if($apts->count() > 0)
                                <div class="panel panel-default ">
                                    <div class="panel-heading " role="tab" id="apartments_heading">
                                        <h4 class="panel-title">
                                            <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#apartments_detail" aria-expanded="false" aria-controls="collapseTwo">
                                                Other Units at this Address
                                            </a>
                                        </h4>
                                    </div>
                                    <div id="apartments_detail" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo">
                                        <div class="panel-body">
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <td>
                                                            Unit
                                                        </td>
                                                        <td>
                                                            Profile
                                                        </td>
                                                    </tr>
                                                </thead>
                                                @foreach($apts as $apt)
                                                    <tr>
                                                        <td>
                                                            {{$apt->unit}}
                                                        </td>
                                                        <td>
                                                            <a class="btn btn-sm btn-primary" href="{{action('\CityNexus\CityNexus\Http\PropertyController@getShow', ['id' => $apt->id])}}">Details</a>
                                                        </td>
                                                    </tr>
                                                @endforeach

                                            </table>
                                        </div>
                                    </div>
                                </div>
                                @endif
                                @foreach($datasets as $key => $dataset)
                                <div class="panel panel-default ">
                                    <div class="panel-heading " role="tab" id="{{preg_replace('/\s+/', '_', $key)}}_heading">
                                        <h4 class="panel-title">
                                            <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#{{preg_replace('/\s+/', '_', $key)}}_detail" aria-expanded="false" aria-controls="collapseTwo">
                                                {{$tables->find($key)->table_title}}
                                            </a>
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

                    @include('citynexus::property._images')

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

                    @if($property->location_id != null && 'local' != env('APP_ENV'))
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


@if($property->location_id != null)
    <script>
        function initialize() {
            var point = {lat: {{$property->location->lat}}, lng:{{$property->location->long}} };
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
@endif


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
                url: "{{action('\CityNexus\CityNexus\Http\PropertyController@postAssociateTag')}}",
                type: 'post',
                data: {
                    _token: "{{csrf_token()}}",
                    property_id: {{$property->id}},
                    tag: tag
                }
            }).success( function( data ) {
                $("#pending").addClass('hidden');
                $('#new-tag-input').val(null);
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
            url: "{{action('\CityNexus\CityNexus\Http\PropertyController@postRemoveTag')}}",
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

<script>
    function viewMeta( message , name)
    {
        var newTitle = 'Metadata for ' + name;
        triggerModal(newTitle, message);
    }
</script>

<script>
    function addImage()
    {
        var title = 'Add Image';
        var uploader = "<form action='{{action('\CityNexus\CityNexus\Http\ImageController@postUpload')}}' method='post' enctype='multipart/form-data'>'" +
                        '{!! csrf_field() !!}' +
                        "<input type='hidden' name='property_id' value='{{$property->id}}'>" +
                        "<input type='file' name='image'>" +
                        "<label for='caption'>Caption</label>" +
                        "<input class='form-control' type='text' name='caption'>" +
                        "<label for='description'>Description</label>" +
                        "<textarea class='form-control' name='description'></textarea>" +
                        "<br><br><input class='btn btn-primary' type='submit' value='Upload Image'>";
        triggerModal(title, uploader);
    }

    function showImage(id)
    {
        $.ajax({
            url: '{{action('\CityNexus\CityNexus\Http\ImageController@getShow')}}/' + id,
        }).success(function(data){
            var image = '<a href="' + data.source + '" target="_blank"><img style="max-width: 90%" class="model_image" src="' + data.source + '"/></a>'+
                                @can('citynexus', ['property', 'delete'])
                                '<br><a class="pull-right" href="/citynexus/image/delete/' + id + '">' +
                                '<i class="fa fa-trash"></i> </a>' +
                                @endcan
                            '<p>' + data.description + '</p>';
                triggerModal(data.caption, image);

        });
    }

</script>

@endpush

