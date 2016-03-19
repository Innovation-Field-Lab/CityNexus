@extends(config('tabler.template'))

@section(config('tabler.section'))
        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="panel-title">
                    All Data Sets
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
            update.addClass('hidden');
            update.html('<span class="glyphicon glyphicon-hourglass"></span> Refreshing Score');

            $.ajax({
                url: "/{{config('citynexus.root_directory')}}/risk-score/update-score",
                method: "POST",
                data: {
                    _token: "{{csrf_token()}}",
                    score_id: id
                }
            }).success(function( data ){

            })
        }
    }

</script>

@endpush
