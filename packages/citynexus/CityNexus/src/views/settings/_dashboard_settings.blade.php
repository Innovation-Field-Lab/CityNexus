<?php
use CityNexus\CityNexus\Widget;

    $widgets = json_decode(setting('globalDashboard'));
    $user_widgets = Widget::where('user_id', \Illuminate\Support\Facades\Auth::getUser()->id)->get();
    $global_widgets = Widget::whereNull('user_id')->get();

    foreach($widgets as $i)
        {
            $widget_list[$i] = true;
        }

?>

<div class="col-xs-6">
    <h4>Global Dashboard Widgets</h4>

    <ul id="active-dashboard" class="list-group connectedSortable">
        @foreach($widgets as $i)
            <li class="list-group-item" id="{{$i}}"><i class="fa fa-sort"></i> {{\CityNexus\CityNexus\Widget::find($i)->name}}</li>
        @endforeach
    </ul>
</div>
<div class="col-xs-6">
    <h4>Unused Widgets</h4>

    <ul id="inactive-dashboard" class="list-group connectedSortable">
        @foreach($user_widgets as $i)
            @unless(isset($widget_list[$i->id]))
            <li class="list-group-item" id="{{$i->id}}"><i class="fa fa-sort"></i> {{$i->name}}</li>
            @endunless
        @endforeach
        @foreach($global_widgets as $i)
            @unless(isset($widget_list[$i->id]))
            <li class="list-group-item" id="{{$i->id}}"><i class="fa fa-sort"></i> {{$i->name}}</li>
            @endunless
        @endforeach
    </ul>

    <button class="btn btn-primary" onclick="createWidget()"><i class="fa fa-plus-circle"></i> Create New Widget</button>

</div>

@push('js_footer')
<script src="https://code.jquery.com/ui/1.12.0/jquery-ui.js"></script>
<script>
    function loadWidgetType()
    {
        var type = $('#widgetType').val();
        $.ajax({
            url: "{{action("\CityNexus\CityNexus\Http\WidgetController@getCreate")}}/" + type,
        }).success(function(data){
            $("#create_widget_form").html(data);
        });
    }

    function createWidget()
    {


        var createWidget = '<form id="widget-create-form">' +
                '<label>Widget Type</label>' +
                '<select id="widgetType" name="type" class="form-control" onchange="loadWidgetType()">' +
                        '<option>Select One</option>' +
                        '<option value="tags">Property Tags</option>' +
                        '<option value="comments">Property Notes</option>' +
                '</select>' +
                '<div id="create_widget_form"></div>' +
                '<br><br>' +
                '<button class="btn btn-primary" id="widCreateSubmit" data-dismiss="modal">Create Widget</button>' +
                '</form>';

        triggerModal('Create New Widget', createWidget);

        $('#widCreateSubmit').click(function(e) {
            e.preventDefault(); // prevents default
            var data = $('#widget-create-form').serialize();
            $.ajax({
                url: "{{action('\CityNexus\CityNexus\Http\WidgetController@postCreate')}}?" + data,
                type: 'POST',
                data: {
                    _token: "{{csrf_token()}}",
                }
            }).success(function (data) {
                $("#inactive-dashboard").append(data);
            });
        });
    }
    $( function() {
        $( "#active-dashboard, #inactive-dashboard" ).sortable({
            axis: 'y',
            connectWith: ".connectedSortable",
            update: function (event, ui) {
                var data = $( "#active-dashboard" ).sortable('toArray').toString();
                // POST to server using $.post or $.ajax
                $.ajax({
                    data: {
                        _token: "{{csrf_token()}}",
                        data: data
                    },
                    type: 'POST',
                    url: '{{action('\CityNexus\CityNexus\Http\CitynexusSettingsController@postUpdateDashboard')}}'
                });
            }
        });
        $( "#active-dashboard" ).disableSelection();
    } );
</script>
@endpush