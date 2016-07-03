<p>Hi {{$assignee->first_name}}!</p>

<p>The following task has been assigned to you:</p>
<b>{{$task->task}}</b><br>
<p>{{$task->description}}</p>';
@if($task->due_by != null)
<p><b>Due Date: {{$task->due->formatLocalized('%d %B %Y')}}</b></p>
@endif
<a style='background-color: #008CBA; /* Blue */
          border: none;
          color: white;
          padding: 7px 24px;
          text-align: center;
          text-decoration: none;
          display: inline-block;
          font-size: 12px;
          border-radius: 8px;'
          href='" . url(action('\CityNexus\CityNexus\Http\TaskController@getShow', ['id' => $task->id])) .>Open in CityNexus</a>