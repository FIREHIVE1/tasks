<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use Validator;
use Response;
use App\Post;
use View;
use App\Task;
use Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    protected $rules =
        [
            'name' => 'required|min:1|max:64|',
            'description' => 'required|min:1|max:128|'
        ];

    public function index()
    {
        //only get the tasks created by user 
        $tasks = Task::where('user_id', Auth::user()->id)->orWhere('assign',Auth::user()->id)->get();
        return view('layouts.base', ['tasks' => $tasks]);
    }

    public function addTask(Request $request)
    {
        $validator = Validator::make(Input::all(), $this->rules);
        if ($validator->fails()) {
            return Response::json(array('errors' => $validator->getMessageBag()->toArray()));
        } else {
            $task = new Task();
            $task->name = $request->input('name');
            $task->description = $request->input('description');
            $task->status = $request->input('status');
            $task->user_id = $request->input('user_id');
            $task->assign = $request->input('assign');
            $task->save();
            return response()->json($task);
        }
    }

    public function editTask($id, Request $request)
    {
        $validator = Validator::make(Input::all(), $this->rules);
        if ($validator->fails()) {
            return Response::json(array('errors' => $validator->getMessageBag()->toArray()));
        } else {
            $task = Task::find($id);
            $task->name = $request->name;
            $task->description = $request->description;
            $task->save();
            return response()->json($task);
        }
    }

    public function deleteTask($id)
    {
        Task::find($id)->delete();
    }
}
