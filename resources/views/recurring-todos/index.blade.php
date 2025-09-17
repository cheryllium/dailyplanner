<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-white leading-tight flex items-center">
            <svg class="w-8 h-8 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
            </svg>
            {{ __('Recurring Todos') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Back to Today's Todos -->
            <div class="mb-8">
                <a href="{{ route('todos.index') }}" class="custom-link text-lg font-semibold flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Today's Todos
                </a>
            </div>
            
            <div class="flex flex-col lg:flex-row gap-8">
                <!-- Left Column: Recurring Todos by Day -->
                <div class="flex-1 lg:w-3/5">
                    <div class="todo-card">
                        <div class="p-8">
                            <h3 class="text-2xl font-bold mb-8" style="color: var(--color-gray)">Recurring Todos by Day</h3>
                        
                            @foreach($daysOfWeek as $day)
                                <div class="mb-8">
                                    <h4 class="font-bold text-xl mb-4 capitalize flex items-center" style="color: var(--color-primary-yellow)">
                                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                                        </svg>
                                        {{ $day }}
                                    </h4>
                                    
                                    @if($recurringTodos->has($day) && $recurringTodos[$day]->count() > 0)
                                        <div class="space-y-4 ml-6">
                                            @foreach($recurringTodos[$day] as $recurringTodo)
                                                <div class="todo-item">
                                                    <div class="flex items-start justify-between">
                                                        <div class="flex-1" id="recurring-todo-{{ $recurringTodo->id }}">
                                                            <!-- Title Section -->
                                                            <div class="title-section">
                                                                <!-- Title Display -->
                                                                <h5 class="title-display font-bold text-lg interactive-hover" style="color: var(--color-gray)" 
                                                                    onclick="editRecurringTodo({{ $recurringTodo->id }}, 'title')">
                                                                    {{ $recurringTodo->title }}
                                                                </h5>
                                                                <!-- Title Edit (hidden by default) -->
                                                                <input type="text" 
                                                                       class="title-edit form-input-modern font-bold text-lg w-full hidden" 
                                                                       value="{{ $recurringTodo->title }}"
                                                                       onblur="saveRecurringTodo({{ $recurringTodo->id }}, 'title', this.value)"
                                                                       onkeydown="handleRecurringTodoEditKeydown(event, {{ $recurringTodo->id }}, 'title', this)">
                                                            </div>
                                                            
                                                            <!-- Description Section -->
                                                            <div class="description-section mt-3">
                                                                @if($recurringTodo->description)
                                                                    <!-- Description Display -->
                                                                    <p class="description-display interactive-hover" style="color: var(--color-gray)" 
                                                                       onclick="editRecurringTodo({{ $recurringTodo->id }}, 'description')">
                                                                        {{ $recurringTodo->description }}
                                                                    </p>
                                                                @else
                                                                    <!-- Empty Description Display -->
                                                                    <p class="description-display interactive-hover italic" style="color: var(--color-border-muted)" 
                                                                       onclick="editRecurringTodo({{ $recurringTodo->id }}, 'description')">
                                                                        Click to add description
                                                                    </p>
                                                                @endif
                                                                <!-- Description Edit (hidden by default) -->
                                                                <textarea class="description-edit form-input-modern w-full hidden" 
                                                                          rows="3"
                                                                          placeholder="Enter description (optional)"
                                                                          onblur="saveRecurringTodo({{ $recurringTodo->id }}, 'description', this.value)"
                                                                          onkeydown="handleRecurringTodoEditKeydown(event, {{ $recurringTodo->id }}, 'description', this)">{{ $recurringTodo->description ?? '' }}</textarea>
                                                            </div>
                                                        </div>
                                                        <div class="ml-4">
                                                            <form method="POST" action="{{ route('recurring-todos.destroy', $recurringTodo) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this recurring todo?')">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="p-2 rounded-lg hover:scale-110 transition-all duration-200" style="color: var(--color-primary-red)">
                                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                                    </svg>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="ml-6 text-center py-4">
                                            <p style="color: var(--color-border-muted)">No recurring todos for {{ $day }}</p>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Right Column: Add New Form -->
                <div class="lg:w-2/5 lg:flex-shrink-0">
                    <div class="todo-card">
                        <div class="p-8">
                            <h3 class="text-2xl font-bold mb-6" style="color: var(--color-gray)">Add New Recurring Todo</h3>
                            <form method="POST" action="{{ route('recurring-todos.store') }}">
                                @csrf
                                <div class="space-y-6">
                                    <div>
                                        <label class="block text-sm font-bold mb-2" style="color: var(--color-gray)">Title</label>
                                        <input id="title" name="title" type="text" class="form-input-modern w-full" value="{{ old('title') }}" placeholder="e.g., Weekly grocery shopping" required autofocus />
                                        <x-input-error class="mt-2" :messages="$errors->get('title')" />
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold mb-2" style="color: var(--color-gray)">Description (optional)</label>
                                        <textarea id="description" name="description" class="form-input-modern w-full" rows="3" placeholder="Add some details...">{{ old('description') }}</textarea>
                                        <x-input-error class="mt-2" :messages="$errors->get('description')" />
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold mb-3" style="color: var(--color-gray)">Days of Week</label>
                                        <div class="grid grid-cols-2 gap-3">
                                            @foreach($daysOfWeek as $day)
                                                <label class="flex items-center p-3 rounded-xl border-2 cursor-pointer hover:scale-105 transition-all duration-200" style="border-color: var(--color-border-muted)" onmouseover="this.style.borderColor='var(--color-primary-yellow)'; this.style.background='var(--color-yellow-light)'" onmouseout="this.style.borderColor='var(--color-border-muted)'; this.style.background='transparent'">
                                                    <input type="checkbox" name="days_of_week[]" value="{{ $day }}" 
                                                           class="w-4 h-4 rounded" style="color: var(--color-primary-yellow)" 
                                                           {{ in_array($day, old('days_of_week', [])) ? 'checked' : '' }}>
                                                    <span class="ml-3 font-medium" style="color: var(--color-gray)">{{ ucfirst($day) }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                        <x-input-error class="mt-2" :messages="$errors->get('days_of_week')" />
                                        <x-input-error class="mt-2" :messages="$errors->get('days_of_week.*')" />
                                    </div>
                                    <div class="pt-4">
                                        <button type="submit" class="btn-primary w-full flex items-center justify-center">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                            </svg>
                                            Add Recurring Todo
                                        </button>
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
