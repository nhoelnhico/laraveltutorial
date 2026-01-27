<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Task Manager') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="{ search: '' }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl p-6 border border-gray-100">
                
                <div class="mb-8 flex items-center justify-between bg-gradient-to-r from-indigo-50 to-blue-50 p-6 rounded-2xl border border-indigo-100">
                    <div>
                        <h3 class="text-indigo-900 font-bold text-lg">Daily Progress</h3>
                        <p class="text-indigo-600 text-sm">You are doing great!</p>
                    </div>
                    <div class="text-right">
                        <span class="text-3xl font-black text-indigo-700">{{ $tasks->where('is_completed', true)->count() }}</span>
                        <span class="text-indigo-400 font-bold text-lg">/ {{ $tasks->count() }}</span>
                    </div>
                </div>

                <form action="{{ url('/tasks') }}" method="POST" class="mb-8 p-6 bg-gray-50 rounded-2xl border border-gray-200 shadow-sm">
                    @csrf
                    <div class="flex flex-col lg:flex-row gap-4 items-end">
                        <div class="flex-1 w-full">
                            <label class="block text-[10px] font-black uppercase text-gray-500 mb-1 ml-1">Task Title</label>
                            <input type="text" name="title" placeholder="What needs to be done?" required
                                   class="w-full border-gray-300 rounded-xl shadow-sm focus:ring-indigo-500 focus:border-indigo-500 py-3">
                        </div>
                        
                        <div class="w-full lg:w-48">
                            <label class="block text-[10px] font-black uppercase text-gray-500 mb-1 ml-1">Priority</label>
                            <select name="priority" class="w-full border-gray-300 rounded-xl shadow-sm focus:ring-indigo-500 py-3">
                                <option value="low">Low</option>
                                <option value="medium" selected>Medium</option>
                                <option value="high">High</option>
                            </select>
                        </div>

                        <div class="w-full lg:w-auto">
                            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-8 rounded-xl transition shadow-lg shadow-indigo-200">
                                + Add Task
                            </button>
                        </div>
                    </div>
                </form>

                <div class="relative mb-8 px-1">
                    <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input type="text" x-model="search" placeholder="Search your tasks..." 
                           class="w-full pl-12 pr-4 py-3 border-gray-200 rounded-2xl text-sm focus:ring-indigo-500 focus:border-indigo-500 shadow-sm transition-all bg-white hover:border-indigo-100">
                </div>

                <div class="space-y-3">
                    @forelse($tasks as $task)
                        <div x-show="search === '' || '{{ strtolower($task->title) }}'.includes(search.toLowerCase())" 
                             x-data="{ open: false }" 
                             class="flex items-center justify-between p-4 bg-white rounded-xl border border-gray-200 shadow-sm hover:border-indigo-300 transition-all group">
                            
                            <div class="flex items-center gap-4 flex-1 min-w-0">
                                <form action="{{ url('/tasks/'.$task->id) }}" method="POST" class="shrink-0">
                                    @csrf @method('PATCH')
                                    <input type="checkbox" onChange="this.form.submit()" {{ $task->is_completed ? 'checked' : '' }} 
                                           class="w-5 h-5 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer">
                                </form>

                                <span class="truncate font-semibold text-gray-700 {{ $task->is_completed ? 'line-through text-gray-300' : '' }}" title="{{ $task->title }}">
                                    {{ $task->title }}
                                </span>
                            </div>

                            <div class="flex items-center gap-4 shrink-0 ml-4">
                                <span class="px-2.5 py-1 text-[10px] font-black rounded uppercase tracking-widest
                                    {{ $task->priority == 'high' ? 'bg-red-100 text-red-700' : ($task->priority == 'medium' ? 'bg-orange-100 text-orange-700' : 'bg-green-100 text-green-700') }}">
                                    {{ $task->priority }}
                                </span>
                                
                                <div class="flex items-center gap-1 border-l pl-3 border-gray-100">
                                    <button @click="open = true" class="p-2 text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </button>

                                    <form action="{{ url('/tasks/'.$task->id) }}" method="POST" class="inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition" onclick="return confirm('Delete this task?')">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <div x-show="open" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm px-4" style="display: none;">
                                <div class="bg-white p-8 rounded-2xl shadow-2xl w-full max-w-md" @click.away="open = false">
                                    <form action="{{ url('/tasks/'.$task->id) }}" method="POST">
                                        @csrf @method('PATCH')
                                        <h3 class="text-xl font-bold mb-5 text-gray-800">Update Task</h3>
                                        <div class="space-y-4">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                                                <input type="text" name="title" value="{{ $task->title }}" class="w-full border-gray-300 rounded-xl focus:ring-indigo-500">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Priority Level</label>
                                                <select name="priority" class="w-full border-gray-300 rounded-xl">
                                                    <option value="low" {{ $task->priority == 'low' ? 'selected' : '' }}>Low</option>
                                                    <option value="medium" {{ $task->priority == 'medium' ? 'selected' : '' }}>Medium</option>
                                                    <option value="high" {{ $task->priority == 'high' ? 'selected' : '' }}>High</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="flex justify-end gap-3 mt-8">
                                            <button type="button" @click="open = false" class="px-5 py-2 text-gray-500 font-semibold hover:bg-gray-100 rounded-xl transition">Cancel</button>
                                            <button type="submit" class="px-6 py-2 bg-indigo-600 text-white font-bold rounded-xl hover:bg-indigo-700 shadow-lg shadow-indigo-200 transition">Save Changes</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-16 bg-gray-50 rounded-2xl border-2 border-dashed border-gray-200">
                            <p class="text-gray-500 font-medium">Your task list is empty. Add a task above to begin!</p>
                        </div>
                    @endforelse
                </div>

            </div>
        </div>
    </div>
</x-app-layout>