<?php
$pagename = "Export Builder";
$section = "exports";
?>

@extends(config('citynexus.template'))

@section(config('citynexus.section'))
<div class="row">
    <div class="col-md-5 " id="map-settings">
        <div class="card-box">
            <h4 class="header-title m-t-0" id="map-name">Choose Data Point</h4>
            <div id="datasets">
                <ul>
                    <li data-jstree='{"opened":false}'> Scores
                        <ul>
                        @foreach($scores as $score)
                            <li data-jstree='{"type":"score"}' onclick="addScore('{{$score->name}}', {{$score->id}})">{{$score->name}}</li>
                        @endforeach
                        </ul>
                    </li>
                    <li data-jstree='{"opened":false}'> Tags
                        <ul>
                            @foreach($tags as $tag)
                                <li data-jstree='{"type":"tag"}' onclick="addTag('{{$tag->tag}}', {{$tag->id}})">{{$tag->tag}}</li>
                            @endforeach
                        </ul>
                    </li>
                    @foreach($datasets as $dataset)
                        <li data-jstree='{"opened":false}'>{{$dataset->table_title}}
                            <ul>
                                @foreach($dataset->schema as $field)
                                    <li data-jstree='{"type":"{{$field->type}}"}'
                                        @if($field->type == 'float' or $field->type == 'integer')
                                            onclick="addNumeric('{{$dataset->table_name}}', '{{$dataset->table_title}}', '{{$field->key}}')"
                                        @else
                                            onclick="addString('{{$dataset->table_name}}', '{{$dataset->table_title}}', '{{$field->key}}')"
                                        @endif

                                    >{{$field->name}}</li>
                                @endforeach
                            </ul>
                        </li>
                    @endforeach
                </ul>
            </div>

        </div>
    </div>
    <div class="col-sm-7">
        <form action="{{action('\CityNexus\CityNexus\Http\ReportController@postExportSave')}}" method="post" class="form">
            {!! csrf_field() !!}
            <div id="elements">
                <div class="card-box">
                    <h4 class="header-title m-t-0">Export Settings</h4>
                    <div class="form-group">
                        <label for="" class="control-label">Export Name</label>
                        <input type="text" name="name" class="form-control">
                    </div>

                    <div class="form-group">
                        <input type="submit" class="btn btn-primary" value="Queue Export">

                    </div>
                </div>
                <div class="card-box">
                    <h4 class="header-title m-t-0">Property</h4>
                    <label for="" class="checkbox-inline">
                        <input type="checkbox" name="elements[property][full_address]" checked> Full Address
                    </label>
                    <label for="" class="checkbox-inline">
                        <input type="checkbox" name="elements[property][parsed_address]">Parsed Address
                    </label>
                    <label for="" class="checkbox-inline">
                        <input type="checkbox" name="elements[property][coordinates]" checked>Coordinates
                    </label>
                </div>
            </div>
        </form>
    </div>
</div>

@stop

@push('style')
<link href="/vendor/citynexus/plugins/jstree/style.css" rel="stylesheet" type="text/css" />

<!--[if lte IE 8]>
<link rel="stylesheet" href="/css/leaflet.ie.css" /> -->

@endpush

@push('js_footer')

    <script src="/vendor/citynexus/plugins/jstree/jstree.min.js"></script>

    <script>
        $('#datasets').jstree({
            'core': {
                'themes': {
                    'responsive': false
                }
            },
            'types': {
                'default': {
                    'icon': 'fa fa-folder'
                },
                'score': {
                    'icon': 'zmdi zmdi-pin'
                },
                'integer': {
                    'icon': 'zmdi zmdi-n-1-square'
                },
                'string': {
                    'icon': 'ti-text'
                },
                'datetime': {
                    'icon': 'ti-calendar'
                },
                'float': {
                    'icon': 'zmdi zmdi-n-1-square'
                },
                'boolean': {
                    'icon': 'zmdi zmdi-file'
                }
            },
            'plugins': ['types']
        });

    </script>

<script>
    var addNumeric = function(dataset, name, key)
    {

        var element;
        var id = Math.random().toString(36).substring(7);
        element = '<div class="card-box" id="' + id + '"> ' +
                '<i class="fa fa-trash-o pull-right" style="color: red; cursor: pointer" onclick="$(\'#' + id + '\').remove()"></i>' +
                '<input type="hidden" name="elements[datasets][' + dataset + '][' + id + '][key]" value="' + key +'">' +
                '<h4 class="header-title m-t-0">' + name + ' > ' + key + ' </h4> ' +
                '<label for="" class="radio-inline">' +
                '<input type="radio" name="elements[datasets][' + dataset + '][' + id + '][method]" value="most-recent" checked> Most Recent Value ' +
                '</label> ' +
                '<label for="" class="radio-inline"> ' +
                '<input type="radio" name="elements[datasets][' + dataset + '][' + id + '][method]" value="sum"> Sum of Values ' +
                '</label> <br>' +
                '<label for="" class="radio-inline"> ' +
                '<input type="radio" name="elements[datasets][' + dataset + '][' + id + '][method]" value="count"> Count of Values ' +
                '</label>' +
                '<label for="" class="radio-inline"> ' +
                '<input type="radio" name="elements[datasets][' + dataset + '][' + id + '][method]" value="mean"> Average of Values ' +
                '</label> ' +
                '<label for="" class="radio-inline"> ' +
                '<input type="radio" name="elements[datasets][' + dataset + '][' + id + '][method]" value="all"> All Values ' +
                '</label> ' +
                '</div>';

        $('#elements').append(element);
    }

    var addString = function (dataset, name, key)
    {

        var element;
        var id = Math.random().toString(36).substring(7);
        element = '<div class="card-box" id="' + id + '"> ' +
                '<i class="fa fa-trash-o pull-right" style="color: red; cursor: pointer" onclick="$(\'#' + id + '\').remove()"></i>' +
                '<input type="hidden" name="elements[datasets][' + dataset + '][' + id + '][key]" value="' + key +'">' +
                '<h4 class="header-title m-t-0">' + name + ' > ' + key +  ' </h4> ' +
                '<label for="" class="radio-inline">' +
                '<input type="radio" name="elements[datasets][' + dataset + '][' + id + '][method]" value="most-recent" checked> Most Recent Value ' +
                '</label>' +
                '<label for="" class="radio-inline">' +
                '<input type="radio" name="elements[datasets][' + dataset + '][' + id + '][method]" value="count"> Count of Values ' +
                '</label>' +
                '<label for="" class="radio-inline"> ' +
                '<input type="radio" name="elements[datasets][' + dataset + '][' + id + '][method]" value="all"> All Values ' +
                '</label> ' +
                '</div>';

        $('#elements').append(element);
    }

    var addScore = function(name, score_id)
    {
        var element;
        var id = Math.random().toString(36).substring(7);
        element = '<div class="card-box" id="' + id + '"> ' +
                '<i class="fa fa-trash-o pull-right" style="color: red; cursor: pointer" onclick="$(\'#' + id + '\').remove()"></i>' +
                '<input type="hidden" name="elements[scores][' + score_id + ']" value="' + score_id +'">' +
                '<h4 class="header-title m-t-0"> Score > ' + name +  ' </h4> ' +
                '</div>';

        $('#elements').append(element);
    }
    var addTag = function(name, tag_id)
    {
        var element;
        var id = Math.random().toString(36).substring(7);
        element = '<div class="card-box" id="' + id + '"> ' +
                '<i class="fa fa-trash-o pull-right" style="color: red; cursor: pointer" onclick="$(\'#' + id + '\').remove()"></i>' +
                '<input type="hidden" name="elements[tags][' + tag_id + ']" value="' + tag_id +'">' +
                '<h4 class="header-title m-t-0"> Score > ' + name +  ' </h4> ' +
                '<label for="" class="radio-inline">' +
                '<input type="radio" name="elements[tags][' + tag_id + '][' + id + '][method]" value="tagged" checked> Currently Tagged ' +
                '</label>' +
                '<label for="" class="radio-inline">' +
                '<input type="radio" name="elements[tags][' + tag_id + '][' + id + '][method]" value="deleted"> Previously Tagged ' +
                '</label>' +
                '<label for="" class="radio-inline"> ' +
                '<input type="radio" name="elements[tags][' + tag_id + '][' + id + '][method]" value="all"> Ever Tagged ' +
                '</label> ' +
                '</div>';

        $('#elements').append(element);
    }
</script>
@endpush