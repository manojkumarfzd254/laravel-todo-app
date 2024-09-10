<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = Task::all();
        return view('tasks.index', compact('tasks'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|unique:tasks'
        ]);

        $data = Task::create([
            'title' => $request->title,
            'status' => 0
        ]);

        return response()->json(['status' => true, 'success' => 'Task added successfully!', 'data' => $data]);
    }

    public function update($id)
    {
        $task = Task::find($id);
        if($task->status == 0){
            $task->status = 1;
            $task->save();
        }

        return response()->json(['status' => true, 'success' => 'Task status updated!', 'data' => $task]);
    }

    public function destroy($id)
    {
        $task = Task::find($id);
        $title = $task->title;
        $task->delete();
        return response()->json(['status' => true, 'success' => 'Task deleted!', 'title' => $title]);
    }
}
