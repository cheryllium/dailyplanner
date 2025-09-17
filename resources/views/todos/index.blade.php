<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-white leading-tight flex items-center">
            <svg class="w-9 h-9 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 20 20">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 4H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V6a2 2 0 00-2-2h-2M8 4a2 2 0 002 2h0a2 2 0 002-2M8 4a2 2 0 012-2h0a2 2 0 012 2m-6 8l2 2 4-4"/>
            </svg>
            {{ __('Today\'s Todos') }} - {{ \Carbon\Carbon::parse($today)->format('M d, Y') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="todo-card">
                <div class="p-8">
                    <!-- Reminders Section -->
                    @if($overdueActivities->count() > 0)
                        <div class="mb-8">
                            <h3 class="text-xl font-bold mb-6 flex items-center" style="color: var(--color-primary-red)">
                                <svg class="w-6 h-6 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                Activity Reminders ({{ $overdueActivities->count() }})
                            </h3>
                            <div class="space-y-4">
                                @foreach($overdueActivities as $activity)
                                    <div class="reminder-card">
                                        <div class="flex items-start justify-between">
                                            <div class="flex items-start space-x-4 flex-1">
                                                <form method="POST" action="{{ route('todos.complete-activity-reminder', $activity) }}">
                                                    @csrf
                                                    <button type="submit" class="custom-checkbox mt-1 hover:scale-110 transition-transform duration-200">
                                                        <svg class="w-4 h-4 text-white opacity-0 group-hover:opacity-100" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                        </svg>
                                                    </button>
                                                </form>
                                                <div class="flex-1">
                                                    <h4 class="font-bold text-lg" style="color: var(--color-gray)">
                                                        {{ $activity->name }}
                                                    </h4>
                                                    @if($activity->description)
                                                        <p class="mt-2" style="color: var(--color-gray)">
                                                            {{ $activity->description }}
                                                        </p>
                                                    @endif
                                                    <div class="mt-3 flex items-center space-x-2">
                                                        <span class="badge-overdue">
                                                            {{ $activity->days_overdue }} day{{ $activity->days_overdue === 1 ? '' : 's' }} overdue
                                                        </span>
                                                        <span class="text-sm" style="color: var(--color-gray)">
                                                            Last: {{ $activity->last_completed_date->diffForHumans(null, false, false, 2) }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <a href="{{ route('activity-trackers.index') }}" class="btn-secondary text-sm px-4 py-2">
                                                Manage
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Today's Calendar Events -->
                    @if($todaysEvents->count() > 0)
                        <div class="mb-8">
                            <h3 class="text-xl font-bold mb-6 flex items-center" style="color: var(--color-primary-blue)">
                                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                Today's Events ({{ $todaysEvents->count() }})
                            </h3>
                            <div class="space-y-3">
                                @foreach($todaysEvents as $event)
                                    <div class="rounded-2xl p-4 border transition-all duration-300"
                                         style="background: linear-gradient(135deg, var(--color-blue-light) 0%, var(--color-yellow-light) 100%); border-color: var(--color-primary-blue);">
                                        <div class="flex items-start justify-between">
                                            <div class="flex-1">
                                                <h4 class="font-bold text-lg" style="color: var(--color-gray)">
                                                    {{ $event->title }}
                                                </h4>
                                                <p class="text-sm mt-1 font-medium" style="color: var(--color-primary-blue)">
                                                    {{ \Carbon\Carbon::parse($event->time)->format('g:i A') }}
                                                </p>
                                                @if($event->description)
                                                    <p class="mt-2 text-sm" style="color: var(--color-gray)">
                                                        {{ $event->description }}
                                                    </p>
                                                @endif
                                            </div>
                                            <a href="{{ route('calendar-events.index') }}" class="btn-secondary text-sm px-4 py-2">
                                                View Calendar
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Celebration Message -->
                    @if($todos->count() > 0 && $todos->every('is_completed'))
                        <div id="celebration-message" class="celebration-card mb-8">
                            <div class="text-6xl mb-4">🎉</div>
                            <h3 class="text-2xl font-bold mb-3" style="color: var(--color-gray)">Fantastic Work!</h3>
                            <p class="text-lg" style="color: var(--color-gray)">All your todos for today are complete! Time to celebrate! 🌟</p>
                        </div>
                    @endif

                    <!-- Today's Todos List -->
                    <div>
                        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-8 space-y-4 sm:space-y-0">
                            <h3 class="text-2xl font-bold" style="color: var(--color-gray)">Today's Tasks</h3>
                            <div class="flex flex-wrap gap-3">
                                <a href="{{ route('activity-trackers.index') }}" class="text-white font-medium px-4 py-2 rounded-lg hover:shadow-md transform hover:-translate-y-0.5 transition-all duration-200 text-sm flex items-center" style="background: var(--color-primary-red)">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                    </svg>
                                    Activity Tracker
                                </a>
                                <a href="{{ route('recurring-todos.index') }}" class="text-white font-medium px-4 py-2 rounded-lg hover:shadow-md transform hover:-translate-y-0.5 transition-all duration-200 text-sm flex items-center" style="background: var(--color-primary-red)">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                    </svg>
                                    Recurring Todos
                                </a>
                            </div>
                        </div>

                        <div class="space-y-5">
                            @if($todos->count() > 0)
                                @foreach($todos as $todo)
                                    <div class="todo-item {{ $todo->is_completed ? 'completed' : '' }}">
                                        <div class="flex items-start justify-between">
                                            <div class="flex items-start space-x-4 flex-1">
                                                <form method="POST" action="{{ route('todos.update', $todo) }}">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="is_completed" value="{{ $todo->is_completed ? 0 : 1 }}">
                                                    <button type="submit" class="custom-checkbox mt-1 {{ $todo->is_completed ? 'checked' : '' }} hover:scale-110 transition-transform duration-200">
                                                        @if($todo->is_completed)
                                                            <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                            </svg>
                                                        @endif
                                                    </button>
                                                </form>
                                                <div class="flex-1">
                                                    <h4 class="font-bold text-lg {{ $todo->is_completed ? 'line-through opacity-60' : '' }}" style="color: var(--color-gray)">
                                                        {{ $todo->title }}
                                                    </h4>
                                                    @if($todo->description)
                                                        <p class="mt-2 {{ $todo->is_completed ? 'line-through opacity-60' : '' }}" style="color: var(--color-gray)">
                                                            {{ $todo->description }}
                                                        </p>
                                                    @endif
                                                    @if($todo->recurringTodo)
                                                        <div class="mt-3">
                                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium" style="background: var(--color-blue-light); color: var(--color-primary-blue)">
                                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                                    <path d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                                                </svg>
                                                                Recurring: {{ ucfirst($todo->recurringTodo->day_of_week) }}
                                                            </span>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <form method="POST" action="{{ route('todos.destroy', $todo) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this todo?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="p-2 rounded-lg hover:scale-110 transition-all duration-200" style="color: var(--color-primary-red); hover:background: var(--color-red-light)">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                        </svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @endif

                            <!-- Add New Todo Form -->
                            <div class="todo-item bg-gradient-to-r from-white/60 to-white/40 backdrop-blur-sm">
                                <form method="POST" action="{{ route('todos.store') }}">
                                    @csrf
                                    <div class="flex items-start space-x-4">
                                        <!-- Non-functional checkbox -->
                                        <div class="custom-checkbox mt-1 opacity-40">
                                            <svg class="w-4 h-4" style="color: var(--color-border-muted)" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                            </svg>
                                        </div>
                                        <div class="flex-1 space-y-4">
                                            <div>
                                                <input id="title" name="title" type="text" class="form-input-modern w-full text-lg font-medium" value="{{ old('title') }}" placeholder="What needs to be done today?" required style="background: rgba(255, 255, 255, 0.8)" />
                                                <x-input-error class="mt-2" :messages="$errors->get('title')" />
                                            </div>
                                            <div>
                                                <textarea id="description" name="description" class="form-input-modern w-full" rows="3" placeholder="Add some details... (optional)" style="background: rgba(255, 255, 255, 0.8); resize: vertical">{{ old('description') }}</textarea>
                                                <x-input-error class="mt-2" :messages="$errors->get('description')" />
                                            </div>
                                            <div class="flex justify-start">
                                                <button type="submit" class="btn-primary flex items-center">
                                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                                    </svg>
                                                    Add Todo
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            @if($todos->count() == 0)
                                <div class="text-center py-12">
                                    <div class="mb-4">
                                        <svg class="w-16 h-16 mx-auto opacity-40" style="color: var(--color-border-muted)" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                        </svg>
                                    </div>
                                    <h4 class="text-xl font-bold mb-2" style="color: var(--color-gray)">Ready for a Fresh Start!</h4>
                                    <p class="text-lg" style="color: var(--color-gray)">No todos for today yet. Add one above or set up some recurring todos!</p>
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
                    celebrationDiv.className = 'celebration-card mb-8';
                    celebrationDiv.innerHTML = `
                        <div class="text-6xl mb-4">🎉</div>
                        <h3 class="text-2xl font-bold mb-3" style="color: var(--color-gray)">Fantastic Work!</h3>
                        <p class="text-lg" style="color: var(--color-gray)">All your todos for today are complete! Time to celebrate! 🌟</p>
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