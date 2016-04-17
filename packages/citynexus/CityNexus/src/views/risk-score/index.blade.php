@extends(config('citynexus.template'))

@section(config('citynexus.section'))

    <div class="alert alert-warning">
        Hey there you innovator you!  So scores are working a LOT faster now. They will update either when you save changes or click on the refresh score button. The score building action will happen in the background but if you navigate away it make cause some issues so if you would, just hold tight for a minute and you should see when the score has finished.
    </div>

        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="panel-title">
                    All Risk Scores
                </div>
            </div>

            <div class="panel-body">
                <table class="table">
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
                                <td>{{$score->updated_at->diffForHumans()}}</td>
                                <td>
                                    @if($score->status == 'pending')
                                    <span class="btn btn-sm btn-default" id="update-{{$score->id}}"><span class="glyphicon glyphicon-hourglass"></span> Refreshing Score</span>

                                    @else
                                    <span class="btn btn-sm btn-primary" onclick="updateScore({{$score->id}})" id="update-{{$score->id}}">Refresh Score</span>
                                    <a class="btn btn-sm btn-primary update-{{$score->id}}" href="/{{config('citynexus.root_directory')}}/risk-score/heat-map?score_id={{$score->id}}">Heat Map</a>
                                    <a class="btn btn-sm btn-primary update-{{$score->id}}" href="/{{config('citynexus.root_directory')}}/risk-score/pin-map?score_id={{$score->id}}">Pin Map</a>
                                    <a class="btn btn-sm btn-primary update-{{$score->id}}" href="/{{config('citynexus.root_directory')}}/risk-score/distribution?score_id={{$score->id}}">Distribution</a>
                                    <a class="btn btn-sm btn-primary update-{{$score->id}}" href="/{{config('citynexus.root_directory')}}/risk-score/edit-score?score_id={{$score->id}}">Edit</a>
                                    <a class="btn btn-sm btn-primary update-{{$score->id}}" href="/{{config('citynexus.root_directory')}}/risk-score/ranking/{{$score->id}}">Ranking</a>
                                    @endif
                                    <a class="btn btn-sm btn-primary update-{{$score->id}}" href="/{{config('citynexus.root_directory')}}/risk-score/duplicate-score?score_id={{$score->id}}">Duplicate</a>
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
            update.html('<span class="glyphicon glyphicon-hourglass"></span> Refreshing Score');

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
                update.addClass('btn-primary');
                update.removeClass('btn-default');
            }).error(function(data){
                alert('Uh oh. Something has gone wrong with this score. Please harass Sean about it. His personal cell phone number is (646) 373-1438.' + data);
            });
        }
    }

</script>

@endpush
