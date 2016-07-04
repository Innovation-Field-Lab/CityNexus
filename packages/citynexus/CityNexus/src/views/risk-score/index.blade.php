<?php
$pagename = 'All Scores';
$section = 'scores';
?>

@extends(config('citynexus.template'))

@section(config('citynexus.section'))

    <div class="portlet">
        {{--<div class="portlet-heading portlet-default">--}}
            {{--<div class="portlet-widgets">--}}
                {{--<a href="javascript:;" data-toggle="reload"><i class="zmdi zmdi-refresh"></i></a>--}}
                {{--<a data-toggle="collapse" data-parent="#accordion1" href="#bg-primary"><i class="zmdi zmdi-minus"></i></a>--}}
                {{--<a href="#" data-toggle="remove"><i class="zmdi zmdi-close"></i></a>--}}
            {{--</div>--}}
            {{--<div class="clearfix"></div>--}}
        {{--</div>--}}

        <div class="portlet-body">
            <table class="table table-hover">
                    <thead>
                    <tr>
                        <th>Score Name</th>
                        <th>Last Scored</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($scores as $score)
                            <tr>
                                <th>{{$score->name}}</th>
                                <td id="age-{{$score->id}}">{{$score->updated_at->diffForHumans()}}</td>
                                <td>
                                    <span class="btn btn-sm btn-primary" onclick="updateScore({{$score->id}})" id="update-{{$score->id}}">Refresh Score</span>

                                    <div class="btn-group">
                                        <button type="button" class="btn btn-default dropdown-toggle waves-effect" data-toggle="dropdown" aria-expanded="false"> Analysis <span class="caret"></span> </button>
                                        <ul class="dropdown-menu">
                                            <li><a href="{{action('\CityNexus\CityNexus\Http\ViewController@getHeatMap')}}?table=citynexus_scores_{{$score->id}}&key=score">Heat Map</a></li>
                                            <li><a href="{{action('\CityNexus\CityNexus\Http\RiskScoreController@getPinMap', ['score_id' => $score->id])}}">Pin Map</a></li>
                                            <li><a href="{{action('\CityNexus\CityNexus\Http\ViewController@getDistribution', ['citynexus_scores_' . $score->id, 'score'])}}">Distribution Chart</a></li>
                                            <li><a href="{{action('\CityNexus\CityNexus\Http\RiskScoreController@getRanking', ['score_id' => $score->id])}}">Rankings</a></li>
                                        </ul>
                                    </div>

                                    <div class="btn-group">
                                        <button type="button" class="btn btn-default dropdown-toggle waves-effect" data-toggle="dropdown" aria-expanded="false"> Actions <span class="caret"></span> </button>
                                        <ul class="dropdown-menu">
                                            <li><a href="{{action('\CityNexus\CityNexus\Http\RiskScoreController@getEditScore', ['score_id' => $score->id])}}">Edit Score</a></li>
                                            <li><a href="{{action('\CityNexus\CityNexus\Http\RiskScoreController@getDuplicateScore', ['score_id' => $score->id])}}">Duplicate Score</a></li>
                                            <li><a href="{{action('\CityNexus\CityNexus\Http\RiskScoreController@getRemoveScore', ['score_id' => $score->id])}}">Remove Score</a></li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                    @endforeach
                </table>
            </div>
        </div>
@stop

@push('js_footer')

<script>
    function updateScore( id )
    {
        var r = confirm('Are you sure you want to refresh this score?  Doing so will delete and rerun all property scores.');
        if(r)
        {
            var update = $('#update-' + id)
            update.removeClass('btn-primary');
            update.addClass('btn-default');
            update.html('<span class="fa fa-spinner fa-spin"></span> Refreshing Score');

            $.ajax({
                url: "/{{config('citynexus.root_directory')}}/risk-score/update-score",
                method: "POST",
                data: {
                    _token: "{{csrf_token()}}",
                    score_id: id
                }
            }).success(function(data)
            {
                update.html('Refresh Score');
                $('#age-' + id).html('Just now!')
                update.addClass('btn-primary');
                update.removeClass('btn-default');
            }).error(function(data){
                Command: toastr["alert"]("'Uh oh. Something has gone wrong with this score. ' + data.Message")
            });
        }
    }

</script>

@endpush
