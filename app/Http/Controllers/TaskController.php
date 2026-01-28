<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller 
{
    /**
     * Display the task list with sorting.
     */
    public function index()
    {
        // Sorts by Priority (High -> Low) and then by Due Date (Soonest first)
        $tasks = auth()->user()->tasks()
            ->orderByRaw("FIELD(priority, 'high', 'medium', 'low')")
            ->orderBy('due_date', 'asc')
            ->get();

        return view('dashboard', compact('tasks'));
    }

    /**
     * Store a newly created task.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|min:3|max:255',
            'priority' => 'required|in:low,medium,high',
            'category' => 'required|string|max:20',
            'due_date' => 'nullable|date'
        ]);

        auth()->user()->tasks()->create([
            'title' => $request->title,
            'priority' => $request->priority,
            'category' => $request->category,
            'due_date' => $request->due_date,
        ]);

        return back()->with('success', 'Task added!');
    }

    /**
     * Update an existing task (Full edit or Checkbox toggle).
     */
    public function update(Request $request, Task $task)
    {
        // Security check
        if ($task->user_id !== Auth::id()) {
            abort(403);
        }

        // Scenario 1: User is using the Inline Edit Form (saving Title/Priority/Category/Date)
        if ($request->has('title')) {
            $validated = $request->validate([
                'title' => 'required|min:3|max:255',
                'priority' => 'required|in:low,medium,high',
                'category' => 'required|string|max:20',
                'due_date' => 'nullable|date',
            ]);

            $task->update($validated);
            return back()->with('success', 'Task updated!');
        }

        // Scenario 2: User clicked the checkbox (toggling completion)
        $task->update([
            'is_completed' => !$task->is_completed
        ]);

        // Trigger confetti signal if task was marked done
        if ($task->is_completed) {
            return back()->with('completed', true);
        }

        return back();
    }

    /**
     * Delete a task.
     */
    public function destroy(Task $task) 
    {
        if ($task->user_id === Auth::id()) {
            $task->delete();
        }
        
        return back()->with('success', 'Task deleted!');
    }

    public function bulkDelete()
{
    // Deletes only the tasks belonging to the logged-in user
    auth()->user()->tasks()->delete();

    return back()->with('success', 'All tasks have been cleared!');
}
}