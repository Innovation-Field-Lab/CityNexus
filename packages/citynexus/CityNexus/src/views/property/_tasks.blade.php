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

@push('js_footer')

<script>
    function addTask()
    {
        var taskForm = '<form action="{{action('\CityNexus\CityNexus\Http\TaskController@postCreate')}}" method="post">' +
                '{{csrf_field()}}' +
                '<input type="hidden" name="model" value="Property">' +
                '<input type="hidden" name="model_id" value="{{$property->id}}">' +
                '<input type="hidden" name="relation" value="tasks">' +
                '<label for="task">Task</label>' +
                '<input class="form-control" type="text" name="task" required>' +
                '<label for="description">Description</label>' +
                '<textarea class="form-control" name="description"></textarea>' +
                '<label for="assigned_to">Assign Task To:</label>' +
                '<select class="form-control" name="assigned_to">' +
                '<option value="">Select One</option>' +
                @foreach($users as $person)
                    '<option value="{{$person->id}}">{{$person->fullname()}}</option>' +
                @endforeach
            '</select>' +
                '<br><br><input type="submit" class="btn btn-primary" value="Create Task"></form>';

        triggerModal('Create Task', taskForm);
    }
</script>

@endpush