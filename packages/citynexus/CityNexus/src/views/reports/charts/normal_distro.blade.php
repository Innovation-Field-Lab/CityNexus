<?php
$pagename = 'Score Distribution: ' . $rs->name;
$section = 'scores';
?>
@extends(config('citynexus.template'))

@section(config('citynexus.section'))

        {{--<div class="portlet-heading portlet-default">--}}
        {{--<div class="portlet-widgets">--}}
        {{--<a href="javascript:;" data-toggle="reload"><i class="zmdi zmdi-refresh"></i></a>--}}
        {{--<a data-toggle="collapse" data-parent="#accordion1" href="#bg-primary"><i class="zmdi zmdi-minus"></i></a>--}}
        {{--<a href="#" data-toggle="remove"><i class="zmdi zmdi-close"></i></a>--}}
        {{--</div>--}}
        {{--<div class="clearfix"></div>--}}
        {{--</div>--}}

            <div class="col-sm-9 ">
                <div class="card-box">
                    <div id="chart"></div>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="portlet">
                    <div class="portlet-heading portlet-default">
                        <h3 class="portlet-title">Data Set Options</h3>
                    </div>
                    <div class="panel-body">
                        <div class="list-group">
                            <a href="/{{config('citynexus.root_directory')}}/risk-score/distribution/{{$rs->id}}?default=true" class="list-group-item @if(isset($_GET['default']) && $_GET['default'] == true)active @endif">Exclude &#8804; Zero</a>
                            <a href="/{{config('citynexus.root_directory')}}/risk-score/distribution/{{$rs->id}}?feel=bern" class="list-group-item @if(isset($_GET['feel']) && $_GET['feel'] == 'burn')active @endif">Exclude Top 1%</a>
                            <a href="/{{config('citynexus.root_directory')}}/risk-score/distribution/{{$rs->id}}?feel=malthus" class="list-group-item">Exclude Top 5%</a>
                            <a href="/{{config('citynexus.root_directory')}}/risk-score/distribution/{{$rs->id}}?feel=castro" class="list-group-item">Exclude Top 10%</a>
                            <a href="/{{config('citynexus.root_directory')}}/risk-score/distribution/{{$rs->id}}?with_zeros=true" class="list-group-item">Including &#8804; Zeros</a>
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

    <script src="https://cdnjs.cloudflare.com/ajax/libs/d3/3.5.16/d3.min.js"></script>
<script>

    // Generate a Bates distribution of 10 random variables.
    var values = {!! json_encode($data) !!};

    // A formatter for counts.
    var formatCount = d3.format(",.0f");

    var margin = {top: 10, right: 30, bottom: 30, left: 30},
            width = 800 - margin.left - margin.right,
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

@endpush