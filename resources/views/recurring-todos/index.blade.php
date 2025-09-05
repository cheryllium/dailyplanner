<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Recurring Todos') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Back to Today's Todos -->
            <div class="mb-6">
                <a href="{{ route('todos.index') }}" class="text-blue-600 hover:text-blue-800 text-sm">
                    ← Back to Today's Todos
                </a>
            </div>
            
            <div class="flex flex-col lg:flex-row">
                <!-- Left Column: Recurring Todos by Day -->
                <div class="flex-1 lg:w-3/5 mr-6">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900">
                            <h3 class="text-lg font-medium mb-6">Recurring Todos by Day</h3>
                        
                            @foreach($daysOfWeek as $day)
                                <div class="mb-6">
                                    <h4 class="font-medium text-gray-800 mb-3 capitalize">{{ $day }}</h4>
                                    
                                    @if($recurringTodos->has($day) && $recurringTodos[$day]->count() > 0)
                                        <div class="space-y-3 ml-4">
                                            @foreach($recurringTodos[$day] as $recurringTodo)
                                                <div class="border rounded-lg p-4 bg-gray-50 border-gray-200">
                                                    <div class="flex items-start justify-between">
                                                        <div class="flex-1" id="recurring-todo-{{ $recurringTodo->id }}">
                                                            <!-- Title Section -->
                                                            <div class="title-section">
                                                                <!-- Title Display -->
                                                                <h5 class="title-display font-medium text-gray-900 cursor-pointer hover:bg-gray-100 rounded px-2 py-1 -mx-2 -my-1 transition-colors" 
                                                                    onclick="editRecurringTodo({{ $recurringTodo->id }}, 'title')">
                                                                    {{ $recurringTodo->title }}
                                                                </h5>
                                                                <!-- Title Edit (hidden by default) -->
                                                                <input type="text" 
                                                                       class="title-edit font-medium text-gray-900 w-full border-0 border-b-2 border-gray-300 focus:border-indigo-500 focus:ring-0 bg-transparent px-0 py-1 hidden" 
                                                                       value="{{ $recurringTodo->title }}"
                                                                       onblur="saveRecurringTodo({{ $recurringTodo->id }}, 'title', this.value)"
                                                                       onkeydown="handleRecurringTodoEditKeydown(event, {{ $recurringTodo->id }}, 'title', this)">
                                                            </div>
                                                            
                                                            <!-- Description Section -->
                                                            <div class="description-section mt-1">
                                                                @if($recurringTodo->description)
                                                                    <!-- Description Display -->
                                                                    <p class="description-display text-gray-600 cursor-pointer hover:bg-gray-100 rounded px-2 py-1 -mx-2 transition-colors" 
                                                                       onclick="editRecurringTodo({{ $recurringTodo->id }}, 'description')">
                                                                        {{ $recurringTodo->description }}
                                                                    </p>
                                                                @else
                                                                    <!-- Empty Description Display -->
                                                                    <p class="description-display text-gray-400 cursor-pointer hover:bg-gray-100 rounded px-2 py-1 -mx-2 italic transition-colors" 
                                                                       onclick="editRecurringTodo({{ $recurringTodo->id }}, 'description')">
                                                                        Click to add description
                                                                    </p>
                                                                @endif
                                                                <!-- Description Edit (hidden by default) -->
                                                                <textarea class="description-edit text-gray-600 w-full border-0 border-b-2 border-gray-300 focus:border-indigo-500 focus:ring-0 bg-transparent px-0 py-1 resize-none hidden" 
                                                                          rows="2"
                                                                          placeholder="Enter description (optional)"
                                                                          onblur="saveRecurringTodo({{ $recurringTodo->id }}, 'description', this.value)"
                                                                          onkeydown="handleRecurringTodoEditKeydown(event, {{ $recurringTodo->id }}, 'description', this)">{{ $recurringTodo->description ?? '' }}</textarea>
                                                            </div>
                                                        </div>
                                                        <div class="ml-4 flex space-x-2">
                                                            <form method="POST" action="{{ route('recurring-todos.update', $recurringTodo) }}" class="inline">
                                                                @csrf
                                                                @method('PUT')
                                                                <input type="hidden" name="title" value="{{ $recurringTodo->title }}">
                                                                <input type="hidden" name="description" value="{{ $recurringTodo->description }}">
                                                                <input type="hidden" name="day_of_week" value="{{ $recurringTodo->day_of_week }}">
                                                                <input type="hidden" name="is_active" value="{{ $recurringTodo->is_active ? 0 : 1 }}">
                                                                <button type="submit" class="text-sm {{ $recurringTodo->is_active ? 'text-orange-600 hover:text-orange-800' : 'text-green-600 hover:text-green-800' }}">
                                                                    {{ $recurringTodo->is_active ? 'Deactivate' : 'Activate' }}
                                                                </button>
                                                            </form>
                                                            <form method="POST" action="{{ route('recurring-todos.destroy', $recurringTodo) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this recurring todo?')">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="text-red-600 hover:text-red-800">
                                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                                    </svg>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <p class="text-gray-500 text-sm ml-4">No recurring todos for {{ $day }}</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Right Column: Add New Form -->
                <div class="lg:w-2/5 lg:flex-shrink-0">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900">
                            <h3 class="text-lg font-medium mb-4">Add New Recurring Todo</h3>
                            <form method="POST" action="{{ route('recurring-todos.store') }}">
                                @csrf
                                <div class="space-y-4">
                                    <div>
                                        <x-input-label for="title" :value="__('Title')" />
                                        <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" :value="old('title')" required autofocus />
                                        <x-input-error class="mt-2" :messages="$errors->get('title')" />
                                    </div>
                                    <div>
                                        <x-input-label for="description" :value="__('Description (optional)')" />
                                        <textarea id="description" name="description" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" rows="3">{{ old('description') }}</textarea>
                                        <x-input-error class="mt-2" :messages="$errors->get('description')" />
                                    </div>
                                    <div>
                                        <x-input-label :value="__('Days of Week')" />
                                        <div class="mt-2 grid grid-cols-2 gap-2">
                                            @foreach($daysOfWeek as $day)
                                                <label class="flex items-center">
                                                    <input type="checkbox" name="days_of_week[]" value="{{ $day }}" 
                                                           class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" 
                                                           {{ in_array($day, old('days_of_week', [])) ? 'checked' : '' }}>
                                                    <span class="ml-2 text-sm text-gray-700">{{ ucfirst($day) }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                        <x-input-error class="mt-2" :messages="$errors->get('days_of_week')" />
                                        <x-input-error class="mt-2" :messages="$errors->get('days_of_week.*')" />
                                    </div>
                                    <div class="flex justify-start mt-4">
                                        <x-primary-button>{{ __('Add Recurring Todo') }}</x-primary-button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript for inline editing -->
    <script>
        function editRecurringTodo(recurringTodoId, field) {
            const container = document.getElementById(`recurring-todo-${recurringTodoId}`);
            
            if (field === 'title') {
                const displayElement = container.querySelector('.title-display');
                const editElement = container.querySelector('.title-edit');
                
                displayElement.classList.add('hidden');
                editElement.classList.remove('hidden');
                editElement.focus();
                editElement.select();
            } else if (field === 'description') {
                const displayElement = container.querySelector('.description-display');
                const editElement = container.querySelector('.description-edit');
                
                displayElement.classList.add('hidden');
                editElement.classList.remove('hidden');
                editElement.focus();
                editElement.select();
            }
        }

        function saveRecurringTodo(recurringTodoId, field, value) {
            // Don't save if value is empty for title field
            if (field === 'title' && !value.trim()) {
                alert('Title cannot be empty');
                return;
            }

            const url = `/recurring-todos/${recurringTodoId}`;
            console.log('Making PUT request to:', url);

            const data = {};
            data[field] = value.trim();

            fetch(`/recurring-todos/${recurringTodoId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error saving recurring todo');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error saving recurring todo');
            });
        }

        function handleRecurringTodoEditKeydown(event, recurringTodoId, field, element) {
            if (event.key === 'Enter' && !event.shiftKey) {
                event.preventDefault();
                saveRecurringTodo(recurringTodoId, field, element.value);
            } else if (event.key === 'Escape') {
                // Cancel edit and return to display mode
                const container = document.getElementById(`recurring-todo-${recurringTodoId}`);
                
                if (field === 'title') {
                    const displayElement = container.querySelector('.title-display');
                    const editElement = container.querySelector('.title-edit');
                    
                    editElement.classList.add('hidden');
                    displayElement.classList.remove('hidden');
                } else if (field === 'description') {
                    const displayElement = container.querySelector('.description-display');
                    const editElement = container.querySelector('.description-edit');
                    
                    editElement.classList.add('hidden');
                    displayElement.classList.remove('hidden');
                }
            }
        }
    </script>
</x-app-layout>
