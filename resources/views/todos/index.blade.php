<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Today\'s Todos') }} - {{ \Carbon\Carbon::parse($today)->format('M d, Y') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <!-- Reminders Section -->
                    @if($overdueActivities->count() > 0)
                        <div class="mb-8">
                            <h3 class="text-lg font-medium text-orange-800 mb-4">
                                Reminders ({{ $overdueActivities->count() }})
                            </h3>
                            <div class="space-y-3">
                                @foreach($overdueActivities as $activity)
                                    <div class="border rounded-lg p-4 bg-orange-50 border-orange-200">
                                        <div class="flex items-start justify-between">
                                            <div class="flex items-start space-x-3 flex-1">
                                                <form method="POST" action="{{ route('todos.complete-activity-reminder', $activity) }}">
                                                    @csrf
                                                    <button type="submit" class="mt-1">
                                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <circle cx="12" cy="12" r="10"></circle>
                                                        </svg>
                                                    </button>
                                                </form>
                                                <div class="flex-1">
                                                    <h4 class="font-medium text-gray-900">
                                                        {{ $activity->name }}
                                                    </h4>
                                                    @if($activity->description)
                                                        <p class="text-gray-600 mt-1">
                                                            {{ $activity->description }}
                                                        </p>
                                                    @endif
                                                    <p class="text-sm text-orange-700 mt-1">
                                                        Last completed: {{ $activity->last_completed_date->diffForHumans(null, false, false, 2) }} 
                                                        ({{ $activity->days_overdue }} days overdue)
                                                    </p>
                                                </div>
                                            </div>
                                            <a href="{{ route('activity-trackers.index') }}" class="text-blue-600 hover:text-blue-800 text-sm">
                                                Manage
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif


                    <!-- Celebration Message -->
                    @if($todos->count() > 0 && $todos->every('is_completed'))
                        <div id="celebration-message" class="mb-8 p-6 bg-gradient-to-r from-green-100 to-blue-100 border border-green-200 rounded-lg text-center">
                            <div class="text-4xl mb-2">🎉</div>
                            <h3 class="text-xl font-bold text-green-800 mb-2">Congratulations!</h3>
                            <p class="text-green-700">You've completed all your todos for today! Great work!</p>
                        </div>
                    @endif

                    <!-- Today's Todos List -->
                    <div>
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-lg font-medium">Today's Tasks</h3>
                            <div class="flex space-x-4">
                                <a href="{{ route('activity-trackers.index') }}" class="text-blue-600 hover:text-blue-800">
                                    Activity Tracker
                                </a>
                                <a href="{{ route('recurring-todos.index') }}" class="text-blue-600 hover:text-blue-800">
                                    Manage Recurring Todos
                                </a>
                            </div>
                        </div>

                        <div class="space-y-4">
                            @if($todos->count() > 0)
                                @foreach($todos as $todo)
                                    <div class="border rounded-lg p-4 {{ $todo->is_completed ? 'bg-green-50 border-green-200' : 'bg-white border-gray-200' }}">
                                        <div class="flex items-start justify-between">
                                            <div class="flex items-start space-x-3 flex-1">
                                                <form method="POST" action="{{ route('todos.update', $todo) }}">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="is_completed" value="{{ $todo->is_completed ? 0 : 1 }}">
                                                    <button type="submit" class="mt-1">
                                                        @if($todo->is_completed)
                                                            <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                                            </svg>
                                                        @else
                                                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <circle cx="12" cy="12" r="10"></circle>
                                                            </svg>
                                                        @endif
                                                    </button>
                                                </form>
                                                <div class="flex-1">
                                                    <h4 class="font-medium {{ $todo->is_completed ? 'line-through text-gray-500' : 'text-gray-900' }}">
                                                        {{ $todo->title }}
                                                    </h4>
                                                    @if($todo->description)
                                                        <p class="text-gray-600 mt-1 {{ $todo->is_completed ? 'line-through' : '' }}">
                                                            {{ $todo->description }}
                                                        </p>
                                                    @endif
                                                    @if($todo->recurringTodo)
                                                        <p class="text-xs text-blue-600 mt-1">
                                                            From recurring todo: {{ ucfirst($todo->recurringTodo->day_of_week) }}
                                                        </p>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <form method="POST" action="{{ route('todos.destroy', $todo) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this todo?')">
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
                            @endif

                            <!-- Add New Todo Form -->
                            <div class="border rounded-lg p-4 bg-gray-50 border-gray-200">
                                <form method="POST" action="{{ route('todos.store') }}">
                                    @csrf
                                    <div class="flex items-start space-x-3">
                                        <!-- Non-functional radio button -->
                                        <div class="mt-1">
                                            <svg class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <circle cx="12" cy="12" r="10"></circle>
                                            </svg>
                                        </div>
                                        <div class="flex-1 space-y-3">
                                            <div>
                                                <x-text-input id="title" name="title" type="text" class="block w-full" :value="old('title')" placeholder="Add a new todo..." required />
                                                <x-input-error class="mt-2" :messages="$errors->get('title')" />
                                            </div>
                                            <div>
                                                <textarea id="description" name="description" class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" rows="2" placeholder="Description (optional)">{{ old('description') }}</textarea>
                                                <x-input-error class="mt-2" :messages="$errors->get('description')" />
                                            </div>
                                            <div class="flex justify-start">
                                                <x-primary-button class="text-sm">{{ __('Add Todo') }}</x-primary-button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            @if($todos->count() == 0)
                                <div class="text-center py-8 text-gray-500">
                                    <p>No todos for today yet. Add one below or set up some recurring todos!</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Function to check if all todos are completed and show/hide celebration message
        function checkCelebration() {
            // Only select forms that update todos (have PUT method)
            const todoForms = document.querySelectorAll('form[action*="todos/"][method="POST"] input[name="_method"][value="PUT"]');
            const actualTodoForms = Array.from(todoForms).map(input => input.closest('form'));
            let completedCount = 0;
            
            // Count completed todos by checking for filled SVG icons (has fill attribute)
            actualTodoForms.forEach(form => {
                const svg = form.querySelector('svg');
                if (svg && svg.hasAttribute('fill') && svg.getAttribute('fill') === 'currentColor') {
                    completedCount++;
                }
            });
            
            const celebrationMessage = document.getElementById('celebration-message');
            
            // Only show celebration if there are todos and all are completed
            if (actualTodoForms.length > 0 && completedCount === actualTodoForms.length) {
                if (celebrationMessage) {
                    celebrationMessage.style.display = 'block';
                } else {
                    // Create celebration message if it doesn't exist
                    const todosSection = document.querySelector('.space-y-4');
                    const celebrationDiv = document.createElement('div');
                    celebrationDiv.id = 'celebration-message';
                    celebrationDiv.className = 'mb-8 p-6 bg-gradient-to-r from-green-100 to-blue-100 border border-green-200 rounded-lg text-center';
                    celebrationDiv.innerHTML = `
                        <div class="text-4xl mb-2">🎉</div>
                        <h3 class="text-xl font-bold text-green-800 mb-2">Congratulations!</h3>
                        <p class="text-green-700">You've completed all your todos for today! Great work!</p>
                    `;
                    todosSection.parentNode.insertBefore(celebrationDiv, todosSection);
                }
            } else if (celebrationMessage) {
                celebrationMessage.style.display = 'none';
            }
        }

        // Add event listeners to all todo completion forms
        document.addEventListener('DOMContentLoaded', function() {
            const todoForms = document.querySelectorAll('form[action*="todos/"][method="POST"] input[name="_method"][value="PUT"]');
            const actualTodoForms = Array.from(todoForms).map(input => input.closest('form'));
            
            actualTodoForms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    // Small delay to allow the form submission to complete
                    setTimeout(checkCelebration, 100);
                });
            });

            // Initial check on page load
            checkCelebration();
        });
    </script>
</x-app-layout>