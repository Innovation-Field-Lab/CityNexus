<div class="panel panel-default">
    <div class="panel-heading">
        Property Tasks
    </div>
    <div class="panel-body">
        <div class="list-group task-list" id="property_tasks">
            @foreach($property->tasks()->open()->get() as $task)
                <a href="{{action('\CityNexus\CityNexus\Http\TaskController@getShow', [$task->id])}}" class="list-group-item">
                    <b>{{$task->task}}</b><br>
                    <small>
                    @if($task->assigned_to != null)
                        Assigned to: {{$task->assignee->fullname()}}<br>
                    @endif
                        {{$task->created_at->diffForHumans()}}
                    @if($task->due_at != null)
                        <br>
                        <b>Due: {{$task->due_at->diffForHumans()}}</b>
                    @endif
                    </small>
                </a>
            @endforeach
        </div>
    </div>
</div>
