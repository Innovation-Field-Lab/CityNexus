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
                    @can('citynexus', ['reports', 'save'])
                    @if(!isset($view_id))
                        <li id="save-view-line"><a onclick="saveView()" id="save-view" style="cursor: pointer"> Save as Report View</a></li>
                    @else
                        <li><a onclick="updateView({{$view_id}})" id="save-view" style="cursor: pointer"> Save Report View Updates</a></li>
                    @endif
                    @endcan
                </ul>
            </div>

            <h4 class="header-title m-t-0 m-b-30"> </h4>
            <div id="chart-wrapper">
                <div id='chart'>
                    <div class="alert alert-info">
                        No data selected!  To build a scatter chart select a horizontal and vertical variable.
                    </div>
                </div>
            </div>
        </div>
    </div>
        <div class="col-sm-3">

            <div class="portlet">
                <div class="portlet-heading portlet-default">
                    <h3 class="portlet-title">Vertical Axis</h3>
                </div>
                <div class="panel-body" id="ver_settings">
                    <br>
                    <div class="datasets" id="ver_datasets">
                        <ul>
                            <li data-jstree='{"opened":false}'> Scores
                                <ul>
                                    @foreach($scores as $score)
                                        <li data-jstree='{"type":"score"}' onclick="setAxis('ver', '_score', {{$score->id}}, 'float', 'Score: {{$score->name}}')">{{$score->name}} </li>
                                    @endforeach
                                </ul>
                            </li>
                            @foreach($datasets as $dataset)
                                <li data-jstree='{"opened":false}'>{{$dataset->table_title}}
                                    <ul>
                                        @foreach($dataset->schema as $field)
                                            <li data-jstree='{"type":"{{$field->type}}"}' onclick="setAxis('ver', {{$dataset->id}}, '{{$field->key}}', '{{$field->type}}', '{{$dataset->table_title}}: {{$field->name}}');">{{$field->name}}</li>
                                        @endforeach
                                    </ul>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    <div id="h_datafields"></div>
                </div>
            </div>
            <div class="portlet">
                <div class="portlet-heading portlet-default">
                    <h3 class="portlet-title">Horizontal Axis</h3>
                </div>
                <div class="panel-body" id="hor_settings">
                    <br>
                    <div class="datasets" id="hor_datasets">
                        <ul>
                            <li data-jstree='{"opened":false}'> Scores
                                <ul>
                                    @foreach($scores as $score)
                                        <li data-jstree='{"type":"score"}' onclick="setAxis('hor', '_score', {{$score->id}}, 'float', 'Score: {{$score->name}}')">{{$score->name}} </li>
                                    @endforeach
                                </ul>
                            </li>
                            @foreach($datasets as $dataset)
                                <li data-jstree='{"opened":false}'>{{$dataset->table_title}}
                                    <ul>
                                        @foreach($dataset->schema as $field)
                                            <li data-jstree='{"type":"{{$field->type}}"}' onclick="setAxis('hor', {{$dataset->id}}, '{{$field->key}}', '{{$field->type}}', '{{$dataset->table_title}}: {{$field->name}}');">{{$field->name}}</li>
                                        @endforeach
                                    </ul>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    <div id="h_datafields"></div>
                </div>
            </div>
            <button class="btn btn-primary block" onclick="updateChart()" id="updateButton">Refresh Scatter Chart</button>
        </div>

        @stop
        @push('style')
        <link href="/vendor/citynexus/plugins/jstree/style.css" rel="stylesheet" type="text/css" />

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
                font-size: 14px
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
        <script type="text/javascript" src="/vendor/citynexus/plugins/d3/d3.js"></script>
        <script src="/vendor/citynexus/plugins/jstree/jstree.min.js"></script>

        <script>

            var dataRequest = {ver: {}, hor: {}};

            var setAxis = function(axis, dataset, key, type, tag)
            {
                dataRequest[axis] = {
                    label: tag,
                    dataset: dataset,
                    key: key,
                    scope: null
                };

                console.log(event.target);

                var settings =
                    '<div id="' + axis + '_detail">' +
                        '<span class="label label-default">' + tag + ' <i class="fa fa-remove" style="cursor: pointer" onclick="resetAxis(\'' + axis + '\')"></i></span></br>';

                if(type == 'float' || type == 'integer')
                {
                    if(dataset != '_score')
                    {
                        settings = settings +'<div class=""> ' +
                                '<label> ' +
                                '<input type="radio" name="' + axis + '" id="' + axis + '_scope" value="most-recent" onclick="setScope(\'' + axis + '\', \'most-recent\')" >' +
                                ' Most Recent Record' +
                                '</label>' +
                                '</div>'+
                                '<div class=""> ' +
                                '<label> ' +
                                '<input type="radio" name="' + axis + '" id="' + axis + '_scope" value="count" onclick="setScope(\'' + axis + '\', \'count\')">' +
                                ' Count of Records ' +
                                '</label>' +
                                '</div>' +
                                '<div class=""> ' +
                                '<label> ' +
                                '<input type="radio" name="' + axis + '" id="' + axis + '_scope" value="mean" onclick="setScope(\'' + axis + '\', \'mean\')">' +
                                ' Average of Records ' +
                                '</label>' +
                                '</div>' +
                                '<div class=""> ' +
                                '<label> ' +
                                '<input type="radio" name="' + axis + '" id="' + axis + '_scope" value="sum" onclick="setScope(\'' + axis + '\', \'sum\')">' +
                                ' Sum of Records ' +
                                '</label>' +
                                '</div>';
                    }

                }

                settings = settings +  '</div>';

                $('#' + axis + '_settings').append(settings);
                $('#' + axis + '_datasets').addClass('hidden');
            };

            function resetAxis(axis) {
                $('#' + axis + '_datasets').removeClass('hidden');
                $('#' + axis + '_detail').remove();
            };

            function setScope(axis, scope) {
                console.log(axis);
                dataRequest[axis]['scope'] = scope;
            };

        </script>
        <script>


            function updateChart() {

                $.ajax({
                    url: "{{action('\CityNexus\CityNexus\Http\ViewController@postScatterDataSet')}}?_token={{csrf_token()}}",
                    type: "post",
                    data: dataRequest,
                    success: function (data) {
                        showScatterPlot(data);
                    }
                });
            }


            function showScatterPlot(data) {
                // just to have some space around items.
                var margins = {
                    "left": 50,
                    "right": 30,
                    "top": 30,
                    "bottom": 40
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
                        .text(dataRequest.hor.label + ' [' + dataRequest.ver.label + ']');


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
                    tooltip.html( toTitleCase(d.full_address) + '</br> (' + d.x + ',' + d.y + ')')
                            .style("left", (d3.event.pageX + 5) + "px")
                            .style("top", (d3.event.pageY - 25) + "px");
                })
                        .on("click", function(d){
                            var win = window.open("{{action('\CityNexus\CityNexus\Http\PropertyController@getShow')}}/" + d.property_id , '_blank');
                            win.focus();
                        })
                        .on("mouseout", function(d) {
                            tooltip.transition()
                                    .duration(500)
                                    .style("opacity", 0);
                        });
            }


            function saveView() {
                var v_table = $('#v_dataset').val();
                var h_table = $('#h_dataset').val();
                var v_key = $('#v_datafield').val();
                var h_key = $('#h_datafield').val();
                if(v_key != null && h_key != null)
                {
                    var name = prompt('What name would you like to give this report view?', 'Unnamed View');
                }
                else
                {
                    Command: toastr["warning"]("Uh oh! You must select both a vertical and horizontal varible before saving.")
                }
                if(name != null)
                {
                    $.ajax({
                        url: "{{action('\CityNexus\CityNexus\Http\ViewController@postSaveView')}}",
                        type: 'post',
                        data: {
                            _token: "{{csrf_token()}}",
                            settings: {
                                type: 'Scatter Chart',
                                v_table: v_table,
                                h_table: h_table,
                                v_key: v_key,
                                h_key: h_key
                            },
                            name: name
                        }
                    }).success(function (data) {
                        Command: toastr["success"](name, "Report View Saved");
                        $('#save-view-line').html( data );
                    });
                }
            }
            function updateView( id )
            {
                var v_table = $('#v_dataset').val();
                var h_table = $('#h_dataset').val();
                var v_key = $('#v_datafield').val();
                var h_key = $('#h_datafield').val();
                $.ajax({
                    url: "{{action('\CityNexus\CityNexus\Http\ViewController@postSaveView')}}",
                    type: 'post',
                    data: {
                        _token: "{{csrf_token()}}",
                        settings: {
                            type: 'Scatter Chart',
                            v_table: v_table,
                            h_table: h_table,
                            v_key: v_key,
                            h_key: h_key,
                        },
                        id: id
                    }
                }).success(function(){
                    Command: toastr["success"](name, "Report View Updated");
                });
            }

            @if(isset($settings))
            drawChart('{{$settings->h_table}}', '{{$settings->h_key}}', '{{$settings->v_table}}', '{{$settings->v_key}}');
            @endif
        </script>
        <script>
            $('.datasets').jstree({
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


    @endpush
