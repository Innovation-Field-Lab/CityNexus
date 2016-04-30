<?php
$pagename = 'Scatter Chart';
$section = 'reports';
?>
@extends(config('citynexus.template'))

@section(config('citynexus.section'))


    <div class="col-sm-9 ">
        <div class="card-box">
            <div class="dropdown pull-right">
                <a href="#" class="dropdown-toggle card-drop" data-toggle="dropdown" aria-expanded="false">
                    <i class="zmdi zmdi-more-vert"></i>
                </a>
                <ul class="dropdown-menu" role="menu">
                    <li><a href="#">Action</a></li>
                    <li><a href="#">Another action</a></li>
                    <li><a href="#">Something else here</a></li>
                    <li class="divider"></li>
                    <li><a href="#">Separated link</a></li>
                </ul>
            </div>

            <h4 class="header-title m-t-0 m-b-30">Line Scatter Diagram</h4>
            <div id="chart-wrapper">
                <div id='chart'>
                    <div class="alert alert-info">
                        No data selected!  To build a scatter chart select a horizontal and vertical variable.
                    </div>
                </div>
            </div>
        </div>
    </div>
    <form action="{{action('\CityNexus\CityNexus\Http\ReportsController@getScatterChart')}}">

        <div class="col-sm-3">

        <div class="portlet">
            <div class="portlet-heading portlet-default">
                <h3 class="portlet-title">Horizontal Variable</h3>
            </div>
            <div class="panel-body">
                <select name="h_dataset" class="form-control dataset" id="h_dataset">
                    <option value="">Select One</option>
                    <option value=""></option>
                    <option value="_scores">Existing Scores</option>
                    <option value=""></option>
                    @foreach($datasets as $i)
                        <option value="{{$i->id}}">{{$i->table_title}}</option>
                    @endforeach
                </select>
                <div id="h_datafields"></div>
            </div>
        </div>
        <div class="portlet">
            <div class="portlet-heading portlet-default">
                <h3 class="portlet-title">Vertical Variable</h3>
            </div>
            <div class="portlet-body">
                <select name="v_dataset" class="form-control dataset" id="v_dataset">
                    <option value="">Select One</option>
                    <option value=""></option>
                    <option value="_scores">Existing Scores</option>
                    <option value=""></option>
                    @foreach($datasets as $i)
                        <option value="{{$i->id}}">{{$i->table_title}}</option>
                    @endforeach
                </select>

                <div id="v_datafields"></div>
            </div>
        </div>
    </div>

@stop
@push('style')

<style>
    #chart-wrapper {
        font-family:"Helvetica Neue";
        color: #686765;
    }
    .name {
        float:right;
        color:#27aae1;
    }
    .axis {
        fill: none;
        stroke: #AAA;
        stroke-width: 1px;
    }
    text {
        stroke: none;
        fill: #666666;
        font-size: .6em;
        font-family:"Helvetica Neue"
    }
    .label {
        fill: #414241;
    }
    .node {
        cursor:pointer;
    }
    .dot {
        opacity: .7;
        cursor: pointer;
    }
    .tooltip {
        position: absolute;
        width: 200px;
        height: 28px;
        pointer-events: none;
    }
</style>

@endpush


@push('js_footer')

<script src="https://cdnjs.cloudflare.com/ajax/libs/d3/3.5.16/d3.min.js"></script>

<script>
    $('.dataset').change(function( event ){
        var selectId = event.currentTarget.id;
        if(selectId == 'v_dataset') var axis = 'v';
        if(selectId == 'h_dataset') var axis = 'h';
        var dataset_id = $('#' + selectId).val();
        $.ajax({
            url: '{{action('\CityNexus\CityNexus\Http\ReportsController@getDataFields')}}/' + dataset_id + '/' + axis,
        }).success(function (data) {
            $('#' + axis + '_datafields').html(data);
        }).error(function (data)
        {
            Command: toastr["warning"]("Uh oh! Something went wrong. Check the console log")
            console.log(data)
        })
    });
</script>
    <script type="text/javascript" src="http://mbostock.github.com/d3/d3.v2.js"></script>
    <script>

        function drawChart(hTable, hKey, vTable, vKey){
            $('#chart').html('<div class="fa fa-spinner fa-spin"></div>');

            var dataSet = scatterChart = d3.json("{{action('\CityNexus\CityNexus\Http\ReportsController@getScatterDataSet')}}/" + hTable + '/' + hKey + '/' + vTable + '/' + vKey, function (dataSet) {
            console.log(dataSet);
            // call the method below
            showScatterPlot(dataSet);



            function showScatterPlot(data) {
                // just to have some space around items.
                var margins = {
                    "left": 40,
                    "right": 30,
                    "top": 30,
                    "bottom": 30
                };

                var chartWrapper = $("#chart-wrapper");
                var width = chartWrapper.width();
                var height = window.innerHeight - 300;

                // this will be our colour scale. An Ordinal scale.
                var colors = d3.scale.category10();

                // we add the SVG component to the scatter-load div
                $('#chart').html(null);
                var svg = d3.select("#chart").append("svg").attr("width", width).attr("height", height).append("g")
                        .attr("transform", "translate(" + margins.left + "," + margins.top + ")");

                // this sets the scale that we're using for the X axis.
                // the domain define the min and max variables to show. In this case, it's the min and max prices of items.
                // this is made a compact piece of code due to d3.extent which gives back the max and min of the price variable within the dataset
                var x = d3.scale.linear()
                        .domain(d3.extent(data, function (d) {
                            return d.x;
                        }))
                        // the range maps the domain to values from 0 to the width minus the left and right margins (used to space out the visualization)
                        .range([0, width - margins.left - margins.right]);

                // this does the same as for the y axis but maps from the rating variable to the height to 0.
                var y = d3.scale.linear()
                        .domain(d3.extent(data, function (d) {
                            return d.y;
                        }))
                        // Note that height goes first due to the weird SVG coordinate system
                        .range([height - margins.top - margins.bottom, 0]);

                // we add the axes SVG component. At this point, this is just a placeholder. The actual axis will be added in a bit
                svg.append("g").attr("class", "x axis").attr("transform", "translate(0," + y.range()[0] + ")");
                svg.append("g").attr("class", "y axis");

                // this is our X axis label. Nothing too special to see here.
                svg.append("text")
                        .attr("fill", "#414241")
                        .attr("text-anchor", "end")
                        .attr("x", width / 2)
                        .attr("y", height - 35)
                        .text($('#h_datafield').val() + '[' + $('#h_table_name').val() + ']');


                // this is the actual definition of our x and y axes. The orientation refers to where the labels appear - for the x axis, below or above the line, and for the y axis, left or right of the line. Tick padding refers to how much space between the tick and the label. There are other parameters too - see https://github.com/mbostock/d3/wiki/SVG-Axes for more information
                var xAxis = d3.svg.axis().scale(x).orient("bottom").tickPadding(2);
                var yAxis = d3.svg.axis().scale(y).orient("left").tickPadding(2);

                // this is where we select the axis we created a few lines earlier. See how we select the axis item. in our svg we appended a g element with a x/y and axis class. To pull that back up, we do this svg select, then 'call' the appropriate axis object for rendering.
                svg.selectAll("g.y.axis").call(yAxis);
                svg.selectAll("g.x.axis").call(xAxis);

                // now, we can get down to the data part, and drawing stuff. We are telling D3 that all nodes (g elements with class node) will have data attached to them. The 'key' we use (to let D3 know the uniqueness of items) will be the name. Not usually a great key, but fine for this example.
                var data = svg.selectAll("g.node").data(data, function (d) {
                    return d.full_address;
                });

                // we 'enter' the data, making the SVG group (to contain a circle and text) with a class node. This corresponds with what we told the data it should be above.

                var dataGroup = data.enter().append("g").attr("class", "node")
                        // this is how we set the position of the items. Translate is an incredibly useful function for rotating and positioning items
                        .attr('transform', function (d) {
                            return "translate(" + x(d.x) + "," + y(d.y) + ")";
                        });
                // add the tooltip area to the webpage
                var tooltip = d3.select("body").append("div")
                        .attr("class", "tooltip")
                        .style("opacity", 0);
                // we add our first graphics element! A circle!

                function toTitleCase(str)
                {
                    return str.replace(/\w\S*/g, function(txt){return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();});
                }


                dataGroup.append("circle")
                        .attr("r", 5)
                        .attr("class", "dot")
                        .style("fill", function (d) {
                            // remember the ordinal scales? We use the colors scale to get a colour for our manufacturer. Now each node will be coloured
                            // by who makes the chocolate.
                            return colors(1);
                        }).on("mouseover", function(d) {
                            tooltip.transition()
                                    .duration(200)
                                    .style("opacity", .9);
                            tooltip.html( toTitleCase(d.address) + '</br> (' + d.x + ',' + d.y + ')')
                                    .style("left", (d3.event.pageX + 5) + "px")
                                    .style("top", (d3.event.pageY - 25) + "px");
                        })
                        .on("click", function(d){
                            var win = window.open("{{action('\CityNexus\CityNexus\Http\CitynexusController@getProperty')}}/" + d.property_id , '_blank');
                            win.focus();
                        })
                        .on("mouseout", function(d) {
                            tooltip.transition()
                                    .duration(500)
                                    .style("opacity", 0);
                        });
            }

            $(window).resize(function(){
                showScatterPlot(dataSet);
            })
            });
        }


    </script>

@endpush