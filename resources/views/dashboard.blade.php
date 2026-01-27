<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My Task Manager') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    @if (session('success'))
                        <div id="status-alert" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4 shadow-sm transition-opacity duration-500">
                            {{ session('success') }}
                        </div>
                        <script>
                            setTimeout(() => {
                                const alert = document.getElementById('status-alert');
                                if(alert) {
                                    alert.style.opacity = '0';
                                    setTimeout(() => alert.remove(), 500);
                                }
                            }, 3000);
                        </script>
                    @endif
                    
                    <form action="{{ url('/tasks') }}" method="POST" class="mb-6">
                        @csrf
                        <div class="flex gap-4">
                            <input type="text" name="title" placeholder="What needs to be done?" 
                                   class="flex-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('title') border-red-500 @enderror" 
                                   value="{{ old('title') }}"> 
                            
                            <select name="priority" class="border-gray-300 rounded-md shadow-sm focus:ring-indigo-500">
                                <option value="low">Low</option>
                                <option value="medium" selected>Medium</option>
                                <option value="high">High</option>
                            </select>

                            <x-primary-button>
                                {{ __('Add Task') }}
                            </x-primary-button>
                        </div>
                        @error('title')
                            <p class="text-red-500 text-sm mt-2 font-semibold">{{ $message }}</p>
                        @enderror
                    </form>

                    <hr class="my-6">

                    <h3 class="font-bold text-lg mb-4 text-gray-800">Your Tasks</h3>
                    <div class="space-y-3">
                        @forelse($tasks as $task)
                            <div x-data="{ open: false }" class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border hover:shadow-sm transition-shadow {{ $task->is_completed ? 'opacity-50' : '' }}">
                                
                                <div class="flex items-center gap-3">
                                    <form action="{{ url('/tasks/'.$task->id) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <input type="checkbox" 
                                               onChange="this.form.submit()" 
                                               {{ $task->is_completed ? 'checked' : '' }}
                                               class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                    </form>

                                    <span class="{{ $task->is_completed ? 'line-through text-gray-500' : 'text-gray-700 font-medium' }}">
                                        {{ $task->title }}
                                    </span>
                                </div>
        
                                <div class="flex items-center space-x-6">
                                    <span class="px-3 py-1 text-[10px] font-black rounded-full tracking-wider
                                        {{ $task->priority == 'high' ? 'bg-red-100 text-red-700' : '' }}
                                        {{ $task->priority == 'medium' ? 'bg-yellow-100 text-yellow-700' : '' }}
                                        {{ $task->priority == 'low' ? 'bg-blue-100 text-blue-700' : '' }}">
                                        {{ strtoupper($task->priority) }}
                                    </span>

                                    <div class="flex items-center gap-2">
                                        <button @click="open = true" class="p-1.5 text-indigo-600 hover:bg-indigo-100 rounded-md transition-colors" title="Edit Task">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                              <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                            </svg>
                                        </button>

                                        <form action="{{ url('/tasks/'.$task->id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 text-red-600 hover:bg-red-100 rounded-md transition-colors" title="Delete Task" onclick="return confirm('Delete this task?')">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                                  <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>

                                <div x-show="open" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
                                    <div class="flex items-center justify-center min-h-screen px-4">
                                        <div class="fixed inset-0 bg-gray-500 bg-opacity-75" @click="open = false"></div>
                                        <div class="bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:max-w-lg sm:w-full p-6 z-50">
                                            <form action="{{ url('/tasks/'.$task->id) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <h3 class="text-lg font-bold mb-4 text-gray-900">Update Task Name</h3>
                                                <input type="text" name="title" value="{{ $task->title }}" 
                                                       class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 mb-4">
                                                <div class="flex justify-end gap-2">
                                                    <button type="button" @click="open = false" class="bg-gray-100 px-4 py-2 rounded-md hover:bg-gray-200">Cancel</button>
                                                    <x-primary-button>Save Changes</x-primary-button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8">
                                <p class="text-gray-500 italic">No tasks yet. Start by adding one above!</p>
                            </div>
                        @endforelse
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>