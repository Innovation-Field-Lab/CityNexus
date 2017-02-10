<?php
$pagename = 'Advanced Search';
$section = 'search';
?>

@extends(config('citynexus.template'))

@section(config('citynexus.section'))

<form action="{{action('\CityNexus\CityNexus\Http\SearchController@postAdvancedSearch')}}" method="post" class="form">

    {!! csrf_field() !!}

    <h4 class="header-title m-t-0 m-b-30">Data Set Filters</h4>

    <div class="row">
        <div class="col-sm-4">
            <div class="card-box">
                <div id="datasets">
                    <ul>
                        <li data-jstree='{"opened":false}'> Scores
                            <ul>
                                @foreach($scores as $score)
                                    <li data-jstree='{"type":"score"}' onclick="addScore('{{$score->name}}', {{$score->id}})">{{$score->name}}</li>
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
        <div class="col-sm-7" id="dataset_filters">

        </div>
    </div>


    <h4 class="header-title m-t-0 m-b-30">Comment Filters</h4>

    <div class="card-box">
        <h4 class="header-title m-t-0 m-b-30">Comment Content</h4>
        <div id="comment_content">
            <div class="row">
                <div class="form-group">
                    <div class="col-sm-6" id="comment_filters">
                        <select name="filters[comments][text][0][type]" class="form-control">
                            <option value="">Select One</option>
                            <option value="contains">Comments Contain</option>
                            <option value="contains">Comments Don't Contain</option>
                        </select>
                    </div>
                    <div class="col-sm-5">
                        <input type="text" name="filters[comments][0][test]" class="form-control">
                    </div>
                    <div class="col-sm-1">
                        <span class="pull-right fa fa-plus-circle fa-2x" onclick="addCommentFilter()"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card-box">
        <h4 class="header-title m-t-0 m-b-30">Comment Activity</h4>
        <div class="row">
            <div class="col-lg-4">
                <label class="control-label">Commented on within range</label>
                <input type="checkbox" name="filters[comments][range][on]">
            </div>

            <div class="col-lg-8">
                <div class="input-group">
                    <div class="input-group-addon"><span class="glyphicon glyphicon-calendar fa fa-calendar"></span></div>
                    <input type="text" name="filters[comments][range][dates]"  class="form-control" id="reportrange" value="" />
                </div>
            </div>
        </div>
    </div>
    <h4 class="header-title m-t-0 m-b-30">Tag Filters</h4>
    <div class="col-sm-6">
        <div class="card-box">
            <h4 class="header-title m-t-0 m-b-30">Include Properties Currently Tagged:</h4>
            <div class="row">
                <div class="panel-footer">
                    <div id="new-tag-input">
                        <select class="form-control typeahead" id="include-tags" name="filters[tags][include-current][]" multiple>

                        </select>                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="card-box">
            <h4 class="header-title m-t-0 m-b-30">Exclude Properties Currently Tagged:</h4>
            <div class="row">
                <div class="panel-footer">
                    <div id="new-tag-input">
                        <select class="form-control typeahead" id="include-tags" name="filters[tags][exclude-current][]" multiple>

                        </select>                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6">
        <div class="card-box">
            <h4 class="header-title m-t-0 m-b-30">Include Properties Previously Tagged:</h4>
            <div class="row">
                <div class="panel-footer">
                    <div id="new-tag-input">
                        <select class="form-control typeahead" id="include-tags" name="filters[tags][include-previous][]" multiple>

                        </select>                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="card-box">
            <h4 class="header-title m-t-0 m-b-30">Exclude Properties Previously Tagged:</h4>
            <div class="row">
                <div class="panel-footer">
                    <div id="new-tag-input">
                        <select class="form-control typeahead" id="include-tags" name="filters[tags][exclude-previous][]" multiple>

                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <button class="btn btn-primary">Begin Search</button>
    </div>
</form>
@stop


@push('style')
<link href="/vendor/citynexus/plugins/jstree/style.css" rel="stylesheet" type="text/css" />
<link href="/vendor/citynexus/plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css" rel="stylesheet">
<link href="/vendor/citynexus/plugins/bootstrap-daterangepicker/daterangepicker.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.css">

@endpush

@push('js_footer')

<script src="/vendor/citynexus/plugins/jstree/jstree.min.js"></script>
<script src="/vendor/citynexus/plugins/moment/moment.js"></script>
<script src="/vendor/citynexus/plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<script src="/vendor/citynexus/plugins/bootstrap-daterangepicker/daterangepicker.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.full.min.js"></script>
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

    $('.typeahead').select2({
       data: tags
    });


    $('#reportrange span').html(moment().subtract(29, 'days').format('MMMM D, YYYY') + ' - ' + moment().format('MMMM D, YYYY'));

    $('#reportrange').daterangepicker({
        format: 'MM/DD/YYYY',
        startDate: '01/01/2016',
        endDate: moment(),
        minDate: '01/01/2016',
        dateLimit: {
            days: 60
        },
        showDropdowns: true,
        showWeekNumbers: true,
        timePicker: false,
        timePickerIncrement: 1,
        timePicker12Hour: true,
        ranges: {
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        },
        opens: 'left',
        drops: 'down',
        buttonClasses: ['btn', 'btn-sm'],
        applyClass: 'btn-success',
        cancelClass: 'btn-default',
        separator: ' to ',
        locale: {
            applyLabel: 'Submit',
            cancelLabel: 'Cancel',
            fromLabel: 'From',
            toLabel: 'To',
            customRangeLabel: 'Custom',
            daysOfWeek: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'],
            monthNames: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
            firstDay: 1
        }
    }, function (start, end, label) {
        console.log(start.toISOString(), end.toISOString(), label);
        $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
    });

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
    var addScore = function(name, id)
    {
        var uid = Math.random().toString(36).substring(7);

        var card =
                '<div class="card-box" id="' + uid + '">' +
                    '<b>Score: ' + name + '</b>' +
                '<i class="fa fa-trash-o pull-right" style="color: red; cursor: pointer" onclick="$(\'#' + uid + '\').remove()"></i>' +
                '<input type="hidden" name="filters[scores][' + uid + '][id]" value="' + id + '">' +

                '<div class="row">' +
                        '<div class="col-sm-8"> ' +
                            '<select class="form-control" name="filters[scores][' + uid + '][type]">' +
                                '<option value="include" checked>Include scored properties</option>' +
                                '<option value="exclude">Exclude scored properties</option>' +
                                '<option value=">">Properties with score greater than</option>' +
                                '<option value="<">Properties with score less than</option>' +
                                '<option value="=">Properties with score equal to</option>' +
                            '</select>' +
                        '</div>' +
                        '<div class="col-sm-4">' +
                            '<input type="number" class="form-control" name="filters[scores][' + uid + '][test]">' +
                        '</div>' +
                    '</div>' +
                '</div>';

        $('#dataset_filters').append(card);
    };

    var addNumeric = function(dataset, name, key)
    {
        var id = Math.random().toString(36).substring(7);

        var card =
                '<div class="card-box" id="' + id + '">' +
                '<b>Dataset: ' + name + '</b>' +
                '<i class="fa fa-trash-o pull-right" style="color: red; cursor: pointer" onclick="$(\'#' + id + '\').remove()"></i>' +
                '<input type="hidden" name="filters[datasets][' + dataset + '][' + id + '][key]" value="' + key +'">' +
                '<br><i>Data Point</i></br>' +
                '<label for="" class="radio-inline">' +
                '<input type="radio" name="filters[datasets][' + dataset + '][' + id + '][method]" value="most-recent" checked> Most Recent Value ' +
                '</label> ' +
                '<label for="" class="radio-inline"> ' +
                '<input type="radio" name="filters[datasets][' + dataset + '][' + id + '][method]" value="sum"> Sum of Values ' +
                '</label> <br>' +
                '<label for="" class="radio-inline"> ' +
                '<input type="radio" name="filters[datasets][' + dataset + '][' + id + '][method]" value="count"> Count of Values ' +
                '</label>' +
                '<label for="" class="radio-inline"> ' +
                '<input type="radio" name="filters[datasets][' + dataset + '][' + id + '][method]" value="mean"> Average of Values ' +
                '</label> ' +
                '<label for="" class="radio-inline"> ' +
                '<input type="radio" name="filters[datasets][' + dataset + '][' + id + '][method]" value="max"> Max Value ' +
                '</label> ' +
                '<label for="" class="radio-inline"> ' +
                '<input type="radio" name="filters[datasets][' + dataset + '][' + id + '][method]" value="min"> Min Value ' +
                '</label> ' +
                '<div class="row">' +
                '<div class="col-sm-8"> ' +
                '<select class="form-control" name="filters[datasets][' + dataset + '][' + id + '][filter]">' +
                '<option value="include" selected>Include properties with value</option>' +
                '<option value="exclude">Exclude properties with value.</option>' +
                '<option value=">">Value is greater than</option>' +
                '<option value="<">Value is less than</option>' +
                '<option value="=">Value is equal to</option>' +
                '</select>' +
                '</div>' +
                '<div class="col-sm-4">' +
                '<input type="number" class="form-control" name="filters[datasets][' + dataset + '][' + id + '][test]">' +
                '</div>' +
                '</div>' +
                '</div>';

        $('#dataset_filters').append(card);
    }

    var addString = function(dataset, name, key)
    {
        var id = Math.random().toString(36).substring(7);

        var card =
                '<div class="card-box" id="' + id + '">' +
                '<b>Dataset: ' + name + '</b>' +
                '<i class="fa fa-trash-o pull-right" style="color: red; cursor: pointer" onclick="$(\'#' + id + '\').remove()"></i>' +
                '<input type="hidden" name="filters[datasets][' + dataset + '][' + id + '][key]" value="' + key +'">' +
                '<br><i>Data Point</i></br>' +
                '<label for="" class="radio-inline">' +
                '<input type="radio" name="filters[datasets][' + dataset + '][' + id + '][method]" value="most-recent" checked> Most Recent Value ' +
                '</label> ' +
                '<label for="" class="radio-inline">' +
                '<input type="radio" name="filters[datasets][' + dataset + '][' + id + '][method]" value="any" checked> Any Value ' +
                '</label> ' +
                '<div class="row">' +
                '<div class="col-sm-6"> ' +
                '<select class="form-control" name="filters[datasets][' + dataset + '][' + id + '][filter]">' +
                '<option value="include" checked>Include properties with value</option>' +
                '<option value="exclude">Exclude properties with value</option>' +
                '<option value="contains">Contains</option>' +
                '<option value="notContains">Does not contain</option>' +
                '<option value="==">Is exactly</option>' +
                '<option value="!=">Is not exactly</option>' +
                '</select>' +
                '</div>' +
                '<div class="col-sm-6">' +
                '<input type="text" class="form-control" name="filters[datasets][' + dataset + '][' + id + '][test]">' +
                '</div>' +
                '</div>' +
                '</div>';

        $('#dataset_filters').append(card);
    }
    var addCommentFilter = function(){

        var uid = Math.random().toString(36).substring(7);

        var card = '<div class="row" id="comment-' + uid + '" > ' +
                '<div class="form-group"> ' +
                '<div class="col-sm-6" id="comment_filters"> ' +
                '<select name="filters[comments][text][' + uid + ']" class="form-control"> ' +
                '<option value="">Select One</option> ' +
                '<option value="contains">Comments Contain</option> ' +
                '<option value="contains">Comments Don\'t Contain</option> ' +
                '</select> ' +
                '</div> ' +
                '<div class="col-sm-5"> ' +
                '<input type="text" name="filters[comments][text][' + uid + ']" class="form-control"> ' +
                '</div> ' +
                '<div class="col-sm-1"> ' +
                '<span class="pull-right fa fa-trash-o fa-2x" onclick="$(\'#comment-' + uid + '\').remove()"></span>' +
                '</div>' +
                '</div> ' +
                '</div>';
        $('#comment_content').append(card);
    }
</script>

@endpush