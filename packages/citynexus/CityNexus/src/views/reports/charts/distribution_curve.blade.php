<?php
$pagename = 'Bates Distribution: ';
if(isset($table_name) && isset($key_name)) $pagename .= $key_name . ' on ' . $table_name;
else $pagename .= 'None Selected';
$section = 'reports';
?>
@extends(config('citynexus.template'))

@section(config('citynexus.section'))

            <div class="col-sm-9 ">
                <div class="card-box" id="chart-wrapper">
                    @if(isset($data))
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
                        <div id="chart"></div>
                    @else
                        <div class="alert alert-info">
                            No data selected!  To build a Bates Distribution chart select the table and variable you would like to examine.
                        </div>
                    @endif
                </div>
            </div>
            <div class="col-sm-3">
                @if(isset($data))

                <div class="portlet">
                    <div class="portlet-heading portlet-default">
                        <h3 class="portlet-title">Data Set Options</h3>
                    </div>
                    <div class="panel-body">
                        <div class="list-group">
                            <a href="?default=true" class="list-group-item @if(!isset($_GET['with_zeros']) && !isset($_GET['feel']))active @endif">Exclude &#8804; Zero</a>
                            <a href="?feel=bern" class="list-group-item @if(isset($_GET['feel']) && $_GET['feel'] == 'bern')active @endif">Exclude Top 1%</a>
                            <a href="?feel=malthus" class="list-group-item @if(isset($_GET['feel']) && $_GET['feel'] == 'malthus')active @endif">Exclude Top 5%</a>
                            <a href="?feel=castro" class="list-group-item @if(isset($_GET['feel']) && $_GET['feel'] == 'castro')active @endif">Exclude Top 10%</a>
                            <a href="?with_zeros=true" class="list-group-item @if(isset($_GET['with_zeros']) && $_GET['with_zeros'] == 'true')active @endif">Including &#8804; Zeros</a>
                        </div>
                    </div>
                </div>
                <div class="portlet">
                    <div class="portlet-heading portlet-default">
                        <h3 class="portlet-title">Score Range Stats</h3>
                    </div>
                    <div class="panel-body">
                        <div class="list-group">

                            <div class="list-group-item">
                                Min: {{number_format($stats['min'], 0, '.', ',')}}
                            </div>

                            <div class="list-group-item">
                                10%: {{number_format($data[$stats['bTen']], 0, '.', ',')}}
                            </div>
                            <div class="list-group-item">
                                25%: {{number_format($data[$stats['firstQ']], 0, '.', ',')}}
                            </div>
                            <div class="list-group-item">
                                50%: {{number_format($data[$stats['median']], 0, '.', ',')}}
                            </div>
                            <div class="list-group-item">
                                75%: {{number_format($data[$stats['thirdQ']], 0, '.', ',')}}
                            </div>
                            <div class="list-group-item">
                                90%: {{number_format($data[$stats['tTen']], 0, '.', ',')}}
                            </div>
                            <div class="list-group-item">
                                Max: {{number_format($stats['max'], 0, '.', ',')}}
                            </div>
                            <div class="list-group-item">
                                Mean: {{number_format($stats['mean'], 0, '.', ',')}}
                            </div>
                            <div class="list-group-item">
                                Count: {{number_format($stats['count'], 0, '.', ',')}}
                            </div>

                        </div>
                    </div>
                </div>

            @else
                <div class="portlet">
                    <div class="portlet-heading portlet-default">
                        <h3 class="portlet-title">Examined Dataset</h3>
                    </div>
                    <div class="panel-body">
                        <select name="dataset" class="form-control dataset" id="dataset">
                            <option value="">Select One</option>
                            <option value=""></option>
                            <option value="_scores">Existing Scores</option>
                            <option value=""></option>
                            @foreach($datasets as $i)
                                <option value="{{$i->id}}">{{$i->table_title}}</option>
                            @endforeach
                        </select>
                        <div id="datafields"></div>
                    </div>
                </div>
            @endif
            </div>
        </div>
    </div>


@stop
    @push('style')
<style>

    #chart {
        font: 10px sans-serif;
    }

    .bar rect {
        fill: steelblue;
        shape-rendering: crispEdges;
    }

    .bar text {
        fill: #fff;
    }

    .axis path, .axis line {
        fill: none;
        stroke: #000;
        shape-rendering: crispEdges;
    }

</style>
@endpush


@push('js_footer')
@if(isset($data))

    <script src="https://cdnjs.cloudflare.com/ajax/libs/d3/3.5.16/d3.min.js"></script>
<script>

    var values = {!! json_encode($data) !!};

    // A formatter for counts.
    var formatCount = d3.format(",.0f");
    var chartWrapper = $('#chart-wrapper');

    console.log(chartWrapper.width());
    var margin = {top: 10, right: 30, bottom: 30, left: 30},
            width = chartWrapper.width() - margin.left - margin.right,
            height = 500 - margin.top - margin.bottom;

    var x = d3.scale.linear()
            .domain([0, {{$stats['max']}}])
            .range([0, width]);

    // Generate a histogram using twenty uniformly-spaced bins.
    var data = d3.layout.histogram()
            .bins(x.ticks(20))
            (values);

    var y = d3.scale.linear()
            .domain([0, d3.max(data, function(d) { return d.y; })])
            .range([height, 0]);

    var xAxis = d3.svg.axis()
            .scale(x)
            .orient("bottom");

    var svg = d3.select("#chart").append("svg")
            .attr("width", width + margin.left + margin.right)
            .attr("height", height + margin.top + margin.bottom)
            .append("g")
            .attr("transform", "translate(" + margin.left + "," + margin.top + ")");

    var bar = svg.selectAll(".bar")
            .data(data)
            .enter().append("g")
            .attr("class", "bar")
            .attr("transform", function(d) { return "translate(" + x(d.x) + "," + y(d.y) + ")"; });

    bar.append("rect")
            .attr("x", 1)
            .attr("width", x(data[0].dx) - 1)
            .attr("height", function(d) { return height - y(d.y); });

    bar.append("text")
            .attr("dy", ".75em")
            .attr("y", 6)
            .attr("x", x(data[0].dx) / 2)
            .attr("text-anchor", "middle")
            .text(function(d) { return formatCount(d.y); });

    svg.append("g")
            .attr("class", "x axis")
            .attr("transform", "translate(0," + height + ")")
            .call(xAxis);

</script>
@endif

<script>
    $('#dataset').change(function(){
        var dataset_id = $('#dataset').val();
        $.ajax({
            url: '{{action('\CityNexus\CityNexus\Http\ViewController@getDataFields')}}/' + dataset_id + '/null/distribution',
        }).success(function (data) {
            $('#datafields').html(data);
        }).error(function (data)
        {
            Command: toastr["warning"]("Uh oh! Something went wrong. Check the console log")
            console.log(data)
        })
    });
</script>

@if(isset($data))

<script>
    function saveReport() {
        var table = "{{$table}}";
        var key = "{{$key}}";

        var name = prompt('What name would you like to give this report view?', 'Unnamed Report');

        if(name != null)
        {
            $.ajax({
                url: "{{action('\CityNexus\CityNexus\Http\ViewController@postSaveView')}}",
                type: 'post',
                data: {
                    _token: "{{csrf_token()}}",
                    settings: {
                        type: 'Distribution',
                        table_name: table,
                        key: key,
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
        var table = "{{$table}}";
        var key = "{{$key}}";

        $.ajax({
            url: "{{action('\CityNexus\CityNexus\Http\ViewController@postSaveView')}}",
            type: 'post',
            data: {
                _token: "{{csrf_token()}}",
                settings: {
                    type: 'Distribution',
                    table_name: table,
                    key: key,
                },
                id: id

            }
        }).success(function(){
            Command: toastr["success"](name, "Report View Updated");
        });

    }
</script>

@endif

@endpush