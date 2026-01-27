<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller 
{
    public function index()
    {
        // Sorts by Priority (High -> Medium -> Low) 
        // AND then sorts by Due Date (Soonest first)
        $tasks = auth()->user()->tasks()
            ->orderByRaw("FIELD(priority, 'high', 'medium', 'low')")
            ->orderBy('due_date', 'asc')
            ->get();

        return view('dashboard', compact('tasks'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|min:3|max:255',
            'priority' => 'required|in:low,medium,high',
            'due_date' => 'nullable|date'
        ]);

        auth()->user()->tasks()->create([
            'title' => $request->title,
            'priority' => $request->priority,
            'due_date' => $request->due_date,
        ]);

        return back()->with('success', 'Task created!');
    }

    public function update(Request $request, Task $task)
    {
        if ($task->user_id !== Auth::id()) {
            abort(403);
        }

        // Check if this is a full update (from the edit form)
        if ($request->has('title')) {
            $validated = $request->validate([
                'title' => 'required|min:3|max:255',
                'priority' => 'required|in:low,medium,high',
                'due_date' => 'nullable|date',
            ]);

            $task->update($validated);
            return back()->with('success', 'Task updated!');
        }

        // Otherwise, toggle the completion checkbox
        $task->update([
            'is_completed' => !$task->is_completed
        ]);

        return back();
    }

    public function destroy(Task $task) 
    {
        if ($task->user_id === Auth::id()) {
            $task->delete();
        }
        return back()->with('success', 'Task deleted!');
    }
}