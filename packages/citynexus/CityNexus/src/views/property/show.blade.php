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

if($property->alias_of != null)
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
        <div class="panel-body">
            <div class="col-sm-8">
                @include('citynexus::property._datasets')

                @include('citynexus::property._images')

                @include('citynexus::property._notes')
            </div>
            <div class="col-sm-4">
                <div class="btn-group">
                    <button type="button" class="btn btn-primary dropdown-toggle waves-effect col-sm-12" data-toggle="dropdown" aria-expanded="false"> Property Actions <span class="caret"></span> </button>
                    <br><br>
                    <ul class="dropdown-menu">
                        <li><a onclick="addImage()"> Add Image</a></li>
                        <li><a onclick="addTask()">Add Task</a></li>
                        @can('citynexus', ['property', 'edit'])<li><a onclick="editAddress()">Edit Address</a></li>@endcan
                        <li><a href="{{action('\CityNexus\CityNexus\Http\TablerController@getMergeRecords')}}/{{$property->id}}">Merge Property</a></li>
                        @can('citynexus', ['property', 'delete'])<li><a href="{{action('\CityNexus\CityNexus\Http\PropertyController@getDelete', ['id' => $property->id])}}/{{$property->id}}"><i class="fa fa-trash"></i> Delete Property</a></li>@endcan

                    </ul>
                </div>
                @if($property->location_id != null && 'local' != env('APP_ENV'))
                    <div class="panel panel-default">
                            <div id="pano" style="height: 250px"></div>
                    </div>
                    <div class="panel panel-default">
                        <div id="map" style="height: 250px"></div>
                    </div>
                @endif
                @if($property->tasks()->open()->get()->count() != null)
                    @include('citynexus::property._tasks')
                @endif

                @include('citynexus::property._tags')
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

@if(env('GMAPI_KEY') != null)
<script async defer
        src="{{'https://maps.googleapis.com/maps/api/js?key=' . env('GMAPI_KEY') . '&signed_in=true&callback=initialize'}}">
</script>
@endif


<script>
    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
    })

    function viewMeta( message , name)
    {
        var newTitle = 'Metadata for ' + name;
        triggerModal(newTitle, message);
    }

    function editAddress()
    {
        $.ajax({
            url: "{{action('\CityNexus\CityNexus\Http\PropertyController@getUpdate', ['id' => $property->id])}}",
            method: 'GET',
        }).success(function(data){
            triggerModal('Edit Property', data);
        });
    }

    function unlink(table, id)
    {
        $.ajax({
            url: "{{action('\CityNexus\CityNexus\Http\TablerController@getRelinkRecord')}}/" + table + '/' + id
        }).success(
                function(){
                    $("#" + table + '_' + id).addClass('hidden');
                }
        )
    }

</script>

<script>
    function addTask()
    {
        var taskForm = '<form action="{{action('\CityNexus\CityNexus\Http\TaskController@postCreate')}}" method="post">' +
                '{{csrf_field()}}' +
                '<input type="hidden" name="model" value="Property">' +
                '<input type="hidden" name="model_id" value="{{$property->id}}">' +
                '<input type="hidden" name="relation" value="tasks">' +
                '<label for="task">Task</label>' +
                '<input class="form-control" type="text" name="task" required>' +
                '<label for="description">Description</label>' +
                '<textarea class="form-control" name="description"></textarea>' +
                '<label for="assigned_to">Assign Task To:</label>' +
                '<select class="form-control" name="assigned_to">' +
                '<option value="">Select One</option>' +
                @foreach($users as $person)
                    '<option value="{{$person->id}}">{{$person->fullname()}} @if($person->title != null) ({{$person->title}}) @elseif($person->department != null) ({{$person->department != null}}) @endif</option>' +
                @endforeach
            '</select>' +
                '<br><br><input type="submit" class="btn btn-primary" value="Create Task"></form>';

        triggerModal('Create Task', taskForm);
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

