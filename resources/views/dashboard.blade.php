<x-app-layout>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
    
    <style>
        /* CSS VARIABLES: Handles the dynamic colors for Light and Dark modes */
        :root { 
            --bg-main: #f8f9fa; --card-bg: #ffffff; --text-main: #212529; 
            --border-color: #dee2e6; --header-gradient: linear-gradient(135deg, #4e73df 0%, #224abe 100%); 
        }
        .dark-theme { 
            --bg-main: #121212; --card-bg: #1e1e1e; --text-main: #f8f9fa; 
            --border-color: #333333; --header-gradient: linear-gradient(135deg, #1a1a1a 0%, #000000 100%); 
        }

        body { background-color: var(--bg-main) !important; color: var(--text-main); transition: 0.4s; }
        .card { background-color: var(--card-bg) !important; color: var(--text-main); border: 1px solid var(--border-color); border-radius: 15px; transition: 0.4s; }
        .progress-header { background: var(--header-gradient) !important; color: #ffffff !important; border: none; }
        .list-group-item { background-color: var(--card-bg) !important; color: var(--text-main); border-color: var(--border-color) !important; transition: 0.4s; }
        .form-control, .form-select { background-color: var(--card-bg) !important; color: var(--text-main) !important; border-color: var(--border-color) !important; }

        /* CATEGORY TAG COLORS: Specific styling for the #labels */
        .cat-work { background-color: #e3f2fd; color: #0d47a1; }
        .cat-personal { background-color: #f3e5f5; color: #7b1fa2; }
        .cat-urgent { background-color: #ffebee; color: #c62828; }
        .cat-general { background-color: #e8f5e9; color: #2e7d32; }

        /* THEME TOGGLE SWITCH: The sliding iOS-style button */
        .theme-switch { display: inline-block; height: 28px; position: relative; width: 54px; }
        .theme-switch input { display: none; }
        .slider { background-color: #ccc; bottom: 0; cursor: pointer; left: 0; position: absolute; right: 0; top: 0; border-radius: 34px; transition: .4s; }
        .slider:before { background-color: #fff; bottom: 4px; content: ""; height: 20px; left: 4px; position: absolute; transition: .4s; width: 20px; border-radius: 50%; }
        input:checked + .slider { background-color: #4e73df; }
        input:checked + .slider:before { transform: translateX(26px); }
        
        [x-cloak] { display: none !important; }
        .overdue { color: #ff6b6b !important; font-weight: bold; }
        .task-row:hover { background-color: rgba(0,0,0,0.02); }
        .dark-theme .task-row:hover { background-color: rgba(255,255,255,0.02); }
    </style>

    <div class="container py-5" 
         x-data="{ 
            darkMode: localStorage.getItem('darkMode') === 'true', 
            search: '',
            filterCategory: 'All',
            filterStatus: 'All'
         }" 
         x-init="
            $watch('darkMode', val => localStorage.setItem('darkMode', val));
            @if(session('completed'))
                confetti({ particleCount: 150, spread: 70, origin: { y: 0.6 } });
            @endif
            @if($tasks->count() > 0 && $tasks->where('is_completed', false)->count() === 0)
                confetti({ particleCount: 200, spread: 100, origin: { y: 0.3 }, colors: ['#FFD700', '#FFA500'] });
            @endif
         "
         :class="{ 'dark-theme': darkMode }">

        <div class="row justify-content-center">
            <div class="col-md-11">

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="fw-bold mb-0">Task Manager</h2>
                    <div class="d-flex align-items-center gap-2">
                        <span class="small fw-bold text-secondary" x-text="darkMode ? 'Dark' : 'Light'"></span>
                        <label class="theme-switch shadow-sm">
                            <input type="checkbox" :checked="darkMode" @change="darkMode = !darkMode">
                            <div class="slider"></div>
                        </label>
                    </div>
                </div>

                <div class="card mb-4 shadow-sm progress-header">
                    <div class="card-body p-4 d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="fw-bold mb-0">Daily Progress</h4>
                            <span class="opacity-75">Productivity Tracker</span>
                        </div>
                        <div class="text-end">
                            <h2 class="fw-bold mb-0">{{ $tasks->where('is_completed', true)->count() }} / {{ $tasks->count() }}</h2>
                            <small class="text-uppercase fw-bold opacity-75">Tasks Done</small>
                        </div>
                    </div>
                </div>

                <div class="card mb-4 shadow-sm">
                    <div class="card-body p-4">
                        <form action="{{ url('/tasks') }}" method="POST" class="row g-3">
                            @csrf
                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-secondary">TASK TITLE</label>
                                <input type="text" name="title" class="form-control" placeholder="What's next?" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small fw-bold text-secondary">PRIORITY</label>
                                <select name="priority" class="form-select">
                                    <option value="low">Low</option>
                                    <option value="medium" selected>Medium</option>
                                    <option value="high">High</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small fw-bold text-secondary">CATEGORY</label>
                                <select name="category" class="form-select">
                                    <option value="General">General</option>
                                    <option value="Work">Work</option>
                                    <option value="Personal">Personal</option>
                                    <option value="Urgent">Urgent</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small fw-bold text-secondary">DUE DATE</label>
                                <input type="date" name="due_date" class="form-control">
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100 fw-bold py-2">Add</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <div class="input-group input-group-lg shadow-sm rounded-4 overflow-hidden">
                            <span class="input-group-text bg-white border-0 text-muted">🔍</span>
                            <input type="text" x-model="search" class="form-control border-0 ps-0" placeholder="Search tasks...">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select x-model="filterCategory" class="form-select form-select-lg shadow-sm rounded-4 border-0 text-secondary">
                            <option value="All">All Categories</option>
                            <option value="General">General</option>
                            <option value="Work">Work</option>
                            <option value="Personal">Personal</option>
                            <option value="Urgent">Urgent</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select x-model="filterStatus" class="form-select form-select-lg shadow-sm rounded-4 border-0 text-secondary">
                            <option value="All">All Status</option>
                            <option value="Pending">Pending</option>
                            <option value="Completed">Completed</option>
                        </select>
                    </div>
                </div>

                <div class="d-flex justify-content-end mb-2 px-2" x-show="filterStatus === 'All' && search === ''">
                    <form action="{{ url('/tasks/bulk-delete') }}" method="POST" onsubmit="return confirm('Delete everything?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm text-danger fw-bold border-0 bg-transparent">
                            🗑️ Delete All Tasks
                        </button>
                    </form>
                </div>

                <div class="list-group shadow-sm rounded-4 overflow-hidden">
                    @forelse($tasks as $task)
                        <div 
                            x-data="{ 
                                editing: false, 
                                isCompleted: {{ $task->is_completed ? 'true' : 'false' }},
                                category: '{{ $task->category }}'
                            }"
                            x-show="
                                (search === '' || '{{ strtolower($task->title) }}'.includes(search.toLowerCase())) &&
                                (filterCategory === 'All' || filterCategory === category) &&
                                (filterStatus === 'All' || (filterStatus === 'Completed' && isCompleted) || (filterStatus === 'Pending' && !isCompleted))
                            "
                            class="list-group-item p-3 task-row">
                            
                            <div x-show="!editing" class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center flex-grow-1">
                                    <form action="{{ url('/tasks/'.$task->id) }}" method="POST" class="me-3">
                                        @csrf @method('PATCH')
                                        <input type="checkbox" onChange="this.form.submit()" {{ $task->is_completed ? 'checked' : '' }} class="form-check-input" style="width: 1.5rem; height: 1.5rem; cursor: pointer;">
                                    </form>
                                    <div>
                                        <span class="fw-bold {{ $task->is_completed ? 'text-muted text-decoration-line-through' : '' }}">{{ $task->title }}</span>
                                        <div class="small mt-1 d-flex gap-2">
                                            <span class="badge {{ $task->priority == 'high' ? 'bg-danger' : ($task->priority == 'medium' ? 'bg-warning text-dark' : 'bg-success') }} text-uppercase" style="font-size: 0.6rem;">{{ $task->priority }}</span>
                                            <span class="badge cat-{{ strtolower($task->category) }}" style="font-size: 0.6rem;">#{{ $task->category }}</span>
                                            @if($task->due_date)
                                                <span class="ms-1 {{ \Carbon\Carbon::parse($task->due_date)->isPast() && !$task->is_completed ? 'overdue' : 'text-muted' }}">
                                                    📅 {{ \Carbon\Carbon::parse($task->due_date)->format('M d') }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex gap-2">
                                    <button @click="editing = true" class="btn btn-sm btn-outline-primary border-0">Edit</button>
                                    <form action="{{ url('/tasks/'.$task->id) }}" method="POST">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger border-0">Del</button>
                                    </form>
                                </div>
                            </div>

                            <div x-show="editing" x-cloak>
                                <form action="{{ url('/tasks/'.$task->id) }}" method="POST" class="row g-2">
                                    @csrf @method('PATCH')
                                    <div class="col-md-4"><input type="text" name="title" value="{{ $task->title }}" class="form-control form-control-sm"></div>
                                    <div class="col-md-2">
                                        <select name="priority" class="form-select form-select-sm">
                                            <option value="low" {{ $task->priority == 'low' ? 'selected' : '' }}>Low</option>
                                            <option value="medium" {{ $task->priority == 'medium' ? 'selected' : '' }}>Medium</option>
                                            <option value="high" {{ $task->priority == 'high' ? 'selected' : '' }}>High</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <select name="category" class="form-select form-select-sm">
                                            <option value="General" {{ $task->category == 'General' ? 'selected' : '' }}>General</option>
                                            <option value="Work" {{ $task->category == 'Work' ? 'selected' : '' }}>Work</option>
                                            <option value="Personal" {{ $task->category == 'Personal' ? 'selected' : '' }}>Personal</option>
                                            <option value="Urgent" {{ $task->category == 'Urgent' ? 'selected' : '' }}>Urgent</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2"><input type="date" name="due_date" value="{{ $task->due_date }}" class="form-control form-control-sm"></div>
                                    <div class="col-md-2 d-flex gap-1">
                                        <button type="submit" class="btn btn-success btn-sm w-100">Save</button>
                                        <button type="button" @click="editing = false" class="btn btn-light btn-sm w-100 border">X</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="list-group-item text-center py-5 opacity-50">No tasks found.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>