<x-app-layout>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        [x-cloak] { display: none !important; }
        .task-row:hover { background-color: #f8f9ff; transition: 0.2s; }
        .overdue { color: #dc3545 !important; font-weight: bold; }
        .card { border-radius: 15px; border: none; }
        .form-control, .form-select { border-radius: 10px; }
    </style>

    <div class="container py-5" x-data="{ search: '' }">
        <div class="row justify-content-center">
            <div class="col-md-11">

                <div class="card mb-4 shadow-sm bg-primary text-white">
                    <div class="card-body p-4 d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="fw-bold mb-0">Daily Progress</h4>
                            <span class="opacity-75">Task Management Dashboard</span>
                        </div>
                        <div class="text-end">
                            <h2 class="fw-bold mb-0">{{ $tasks->where('is_completed', true)->count() }} / {{ $tasks->count() }}</h2>
                            <small class="text-uppercase fw-bold opacity-75">Completed</small>
                        </div>
                    </div>
                </div>

                <div class="card mb-4 shadow-sm">
                    <div class="card-body p-4">
                        <form action="{{ url('/tasks') }}" method="POST">
                            @csrf
                            <div class="row g-3">
                                <div class="col-md-5">
                                    <label class="form-label small fw-bold text-muted">TASK TITLE</label>
                                    <input type="text" name="title" class="form-control border-2" placeholder="What needs to be done?" required>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label small fw-bold text-muted">PRIORITY</label>
                                    <select name="priority" class="form-select border-2">
                                        <option value="low">Low</option>
                                        <option value="medium" selected>Medium</option>
                                        <option value="high">High</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small fw-bold text-muted">DUE DATE</label>
                                    <input type="date" name="due_date" class="form-control border-2">
                                </div>
                                <div class="col-md-2 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary w-100 fw-bold py-2 shadow-sm">Add Task</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="input-group input-group-lg mb-4 shadow-sm" style="border-radius: 12px; overflow: hidden;">
                    <span class="input-group-text bg-white border-end-0 text-muted">🔍</span>
                    <input type="text" x-model="search" class="form-control border-start-0 ps-0" placeholder="Filter tasks by name...">
                </div>

                <div class="list-group shadow-sm border-0" style="border-radius: 15px; overflow: hidden;">
                    @forelse($tasks as $task)
                        <div x-show="search === '' || '{{ strtolower($task->title) }}'.includes(search.toLowerCase())" 
                             x-data="{ editing: false }" 
                             class="list-group-item p-3 border-bottom task-row">
                            
                            <div x-show="!editing" class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center flex-grow-1 overflow-hidden">
                                    <form action="{{ url('/tasks/'.$task->id) }}" method="POST" class="me-3">
                                        @csrf @method('PATCH')
                                        <input type="checkbox" onChange="this.form.submit()" {{ $task->is_completed ? 'checked' : '' }} 
                                               class="form-check-input" style="width: 1.5rem; height: 1.5rem; cursor: pointer;">
                                    </form>
                                    
                                    <div class="text-truncate">
                                        <span class="fw-bold h6 mb-0 {{ $task->is_completed ? 'text-muted text-decoration-line-through' : 'text-dark' }}">
                                            {{ $task->title }}
                                        </span>
                                        <div class="d-flex align-items-center mt-1">
                                            <span class="badge {{ $task->priority == 'high' ? 'bg-danger' : ($task->priority == 'medium' ? 'bg-warning text-dark' : 'bg-success') }} text-uppercase me-2" style="font-size: 0.6rem;">
                                                {{ $task->priority }}
                                            </span>
                                            
                                            @if($task->due_date)
                                                <span class="small {{ \Carbon\Carbon::parse($task->due_date)->isPast() && !$task->is_completed ? 'overdue' : 'text-muted' }}">
                                                    📅 {{ \Carbon\Carbon::parse($task->due_date)->format('M d, Y') }}
                                                    <span class="opacity-50 ms-1">({{ \Carbon\Carbon::parse($task->due_date)->diffForHumans() }})</span>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="ms-3 d-flex gap-2">
                                    <button @click="editing = true" class="btn btn-sm btn-outline-primary border-0">Edit</button>
                                    <form action="{{ url('/tasks/'.$task->id) }}" method="POST" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger border-0" onclick="return confirm('Delete this task?')">Del</button>
                                    </form>
                                </div>
                            </div>

                            <div x-show="editing" x-cloak>
                                <form action="{{ url('/tasks/'.$task->id) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <div class="row g-2 align-items-center">
                                        <div class="col-md-5">
                                            <label class="small fw-bold text-muted d-block d-md-none">Title</label>
                                            <input type="text" name="title" value="{{ $task->title }}" class="form-control form-control-sm border-primary" required>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="small fw-bold text-muted d-block d-md-none">Priority</label>
                                            <select name="priority" class="form-select form-select-sm border-primary">
                                                <option value="low" {{ $task->priority == 'low' ? 'selected' : '' }}>Low</option>
                                                <option value="medium" {{ $task->priority == 'medium' ? 'selected' : '' }}>Medium</option>
                                                <option value="high" {{ $task->priority == 'high' ? 'selected' : '' }}>High</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="small fw-bold text-muted d-block d-md-none">Due Date</label>
                                            <input type="date" name="due_date" value="{{ $task->due_date }}" class="form-control form-control-sm border-primary">
                                        </div>
                                        <div class="col-md-2 d-flex gap-1 pt-md-0 pt-2">
                                            <button type="submit" class="btn btn-success btn-sm w-100">Save</button>
                                            <button type="button" @click="editing = false" class="btn btn-light btn-sm w-100 border">Cancel</button>
                                        </div>
                                    </div>
                                </form>
                            </div>

                        </div>
                    @empty
                        <div class="list-group-item text-center py-5 text-muted italic">
                            No tasks found. Relax or add something new!
                        </div>
                    @endforelse
                </div>

            </div>
        </div>
    </div>
</x-app-layout>