<?php

namespace CityNexus\CityNexus\Http;

use App\ApiKey;
use App\User;
use Carbon\Carbon;
use CityNexus\CityNexus\SendEmail;
use CityNexus\CityNexus\Task;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Salaback\Tabler\Table;

class TaskController extends Controller
{
    public function getShow($id, Request $request)
    {
        $task = Task::find($id);
        return view('citynexus::task.show', compact('task'));
    }
    public function postCreate(Request $request)
    {
        // Save Task
        $task = $request->all();


        if($task['assigned_to'] == null) unset($task['assigned_to']);
        $task = Task::create($task);
        $task->created_by = Auth::getUser()->id;
        $task->save();

        // Retrieve Related model
        $model = "\\CityNexus\\CityNexus\\" . $request->get('model');
        $model_id = $request->get('model_id');
        $related = $model::find($model_id);
        $relation = $request->get('relation');

        // Attach Model
        $related->$relation()->attach($task);

        // Send Email Notification
        $assignee = $request->get("assigned_to");

        if($request->get('assigned_to') != null)
        {
            $this->sendNotification($task, $assignee);
        }

        return redirect()->back();
    }

    public function getMarkComplete($id, Request $request)
    {
        $task = Task::find($id);
        $task->completed_at = Carbon::now();
        $task->completed_by = Auth::getUser()->id;
        $task->save();

        if($request->isJson())
        {
            return response();
        }
        else
        {
            Session::flash('flash_success', 'Task Completed');
            return redirect('/');
        }
    }

    private function sendNotification($task, $assignee)
    {

        $assignee = User::find($assignee);
        $message = "<p>Hi " . $assignee->first_name . "!</p>

        <p>The following task has been assigned to you:</p>
        <b>" . $task->task . "</b><br>
        <p> " . $task->description . "</p>";

        if($task->due_by != null)
        {
            $message .= "<p><b>Due Date: " . $task->due_at->formatLocalized('%d %B %Y') . "</b></p>";
        }
        $message .= 'Open task in CityNexus: </br>';
        $message .= url(action('\CityNexus\CityNexus\Http\TaskController@getShow', ['id' => $task->id]));

        $this->dispatch(new SendEmail($assignee->email, 'New Task: ' . $task->task, $message));
        Session::flash('flash_success', "Email sent to task owner");
        return true;
    }
}
