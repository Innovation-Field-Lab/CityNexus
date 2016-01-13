<?php

namespace Alaback\Todo\Http;

use App\Http\Controllers\Controller;

class TodoController extends Controller
{
    public function getUserTodoList()
    {
        return view('todo::todo-list');
    }
}