<?php

namespace App\Http\Controllers;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller {
public function index()
{
    // This sorts High first, then Medium, then Low
    $tasks = auth()->user()->tasks()
        ->orderByRaw("FIELD(priority, 'high', 'medium', 'low')")
        ->get();

    return view('dashboard', compact('tasks'));
}

public function store(Request $request)
{
    $request->validate([
        'title' => 'required',
        'due_date' => 'nullable|date' // Validate the date
    ]);

    auth()->user()->tasks()->create([
        'title' => $request->title,
        'priority' => $request->priority,
        'due_date' => $request->due_date,
    ]);

    return back();
}

    public function destroy(Task $task) {
        if ($task->user_id === Auth::id()) {
            $task->delete();
        }
        return back();
    }

   public function update(Request $request, Task $task)
{
    // If the request has a 'title', we are editing the text
    if ($request->has('title')) {
        $request->validate(['title' => 'required|min:3|max:255']);
        $task->update(['title' => $request->title]);
        return back()->with('success', 'Task renamed!');
    }

    // Otherwise, we are just toggling the checkbox (what we did before)
    $task->update([
        'is_completed' => !$task->is_completed
    ]);

    return back();
}
}
