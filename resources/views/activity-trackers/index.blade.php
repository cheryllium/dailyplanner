<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-white leading-tight flex items-center">
            <svg class="w-8 h-8 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
            {{ __('Activity Tracker') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="todo-card">
                <div class="p-8">
                    <!-- Back to Todos Link -->
                    <div class="mb-8">
                        <a href="{{ route('todos.index') }}" class="custom-link text-lg font-semibold flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                            Back to Today's Todos
                        </a>
                    </div>

                    <!-- Success Message -->
                    @if(session('success'))
                        <div class="mb-8 p-4 rounded-2xl" style="background: var(--color-blue-light); border: 2px solid var(--color-primary-blue); color: var(--color-gray)">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-2" style="color: var(--color-primary-blue)" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                {{ session('success') }}
                            </div>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        <!-- Activities List -->
                        <div class="lg:col-span-2">
                            <h3 class="text-2xl font-bold mb-8" style="color: var(--color-gray)">Your Activities</h3>
                            
                            <div class="space-y-6">
                                @if($activities->count() > 0)
                                    @foreach($activities as $activity)
                                        <div class="activity-card">
                                            <div class="flex items-start gap-6">
                                                <!-- Date Section (Left) -->
                                                <div class="w-56 flex-shrink-0">
                                                    <label class="block text-sm font-bold mb-2" style="color: var(--color-gray)">
                                                        Last completed:
                                                    </label>
                                                    <input 
                                                        type="date" 
                                                        value="{{ $activity->last_completed_date ? $activity->last_completed_date->format('Y-m-d') : '' }}"
                                                        class="form-input-modern w-full"
                                                        data-activity-id="{{ $activity->id }}"
                                                        onchange="updateLastCompleted(this)"
                                                    />
                                                    @if($activity->last_completed_date)
                                                        <p class="text-xs mt-2" style="color: var(--color-border-muted)">
                                                            {{ \Carbon\Carbon::parse($activity->last_completed_date)->diffForHumans() }}
                                                        </p>
                                                    @endif
                                                    
                                                    <div class="mt-4">
                                                        <div class="flex items-center">
                                                            <label class="text-sm font-bold mr-2" style="color: var(--color-gray)">
                                                                Remind every
                                                            </label>
                                                            <input 
                                                                type="number" 
                                                                value="{{ $activity->frequency_days ?? '' }}"
                                                                min="1" 
                                                                max="3650"
                                                                placeholder="30"
                                                                class="form-input-modern w-20 text-center"
                                                                data-activity-id="{{ $activity->id }}"
                                                                onchange="updateFrequency(this)"
                                                            />
                                                            <span class="ml-2 text-sm font-medium" style="color: var(--color-gray)">days</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <!-- Activity Info Section (Right) -->
                                                <div class="flex-1 flex items-start justify-between">
                                                    <div class="flex-1" id="activity-{{ $activity->id }}">
                                                        <!-- Name Section -->
                                                        <div class="name-section">
                                                            <!-- Name Display -->
                                                            <h4 class="name-display font-bold text-xl interactive-hover" style="color: var(--color-gray)" 
                                                                onclick="editActivity({{ $activity->id }}, 'name')">
                                                                {{ $activity->name }}
                                                            </h4>
                                                            <!-- Name Edit (hidden by default) -->
                                                            <input type="text" 
                                                                   class="name-edit form-input-modern font-bold text-xl w-full hidden" 
                                                                   value="{{ $activity->name }}"
                                                                   onblur="saveActivity({{ $activity->id }}, 'name', this.value)"
                                                                   onkeydown="handleEditKeydown(event, {{ $activity->id }}, 'name', this.value)">
                                                                   
                                                            <!-- Due Status Badge -->
                                                            @if($activity->days_until_due !== null)
                                                                <div class="mt-3">
                                                                    @if($activity->days_until_due < 0)
                                                                        <span class="badge-overdue">
                                                                            Overdue by {{ abs($activity->days_until_due) }} day{{ abs($activity->days_until_due) === 1 ? '' : 's' }}
                                                                        </span>
                                                                    @elseif($activity->days_until_due === 0)
                                                                        <span class="badge-due-soon">
                                                                            Due today
                                                                        </span>
                                                                    @elseif($activity->days_until_due <= 3)
                                                                        <span class="badge-due-soon">
                                                                            Due in {{ $activity->days_until_due }} day{{ $activity->days_until_due === 1 ? '' : 's' }}
                                                                        </span>
                                                                    @else
                                                                        <span class="badge-due-later">
                                                                            Due in {{ $activity->days_until_due }} day{{ $activity->days_until_due === 1 ? '' : 's' }}
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                            @endif
                                                        </div>
                                                        
                                                        <!-- Description Section -->
                                                        <div class="description-section mt-3">
                                                            @if($activity->description)
                                                                <!-- Description Display -->
                                                                <p class="description-display interactive-hover" style="color: var(--color-gray)" 
                                                                   onclick="editActivity({{ $activity->id }}, 'description')">
                                                                    {{ $activity->description }}
                                                                </p>
                                                            @else
                                                                <!-- Empty Description Display -->
                                                                <p class="description-display interactive-hover italic" style="color: var(--color-border-muted)" 
                                                                   onclick="editActivity({{ $activity->id }}, 'description')">
                                                                    Click to add description
                                                                </p>
                                                            @endif
                                                            <!-- Description Edit (hidden by default) -->
                                                            <textarea class="description-edit form-input-modern w-full hidden" 
                                                                      rows="3"
                                                                      placeholder="Enter description (optional)"
                                                                      onblur="saveActivity({{ $activity->id }}, 'description', this.value)"
                                                                      onkeydown="handleEditKeydown(event, {{ $activity->id }}, 'description', this.value)">{{ $activity->description ?? '' }}</textarea>
                                                        </div>
                                                    </div>
                                                    <form method="POST" action="{{ route('activity-trackers.destroy', $activity) }}" class="ml-4">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="p-3 rounded-lg hover:scale-110 transition-all duration-200" style="color: var(--color-primary-red)" onclick="return confirm('Are you sure?')">
                                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                            </svg>
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="text-center py-12">
                                        <div class="mb-4">
                                            <svg class="w-16 h-16 mx-auto opacity-40" style="color: var(--color-border-muted)" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                            </svg>
                                        </div>
                                        <h4 class="text-xl font-bold mb-2" style="color: var(--color-gray)">No Activities Yet</h4>
                                        <p class="text-lg" style="color: var(--color-gray)">Start tracking your activities using the form on the right!</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Add Activity Form -->
                        <div class="todo-card">
                            <div class="p-6">
                                <h3 class="text-2xl font-bold mb-6" style="color: var(--color-gray)">Add New Activity</h3>
                                
                                <form method="POST" action="{{ route('activity-trackers.store') }}" class="space-y-6">
                                    @csrf
                                    
                                    <div>
                                        <label for="name" class="block text-sm font-bold mb-2" style="color: var(--color-gray)">Activity Name</label>
                                        <input type="text" name="name" id="name" required
                                               class="form-input-modern w-full"
                                               placeholder="e.g., Refill cat's water"
                                               value="{{ old('name') }}">
                                        @error('name')
                                            <p class="mt-1 text-sm" style="color: var(--color-primary-red)">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="description" class="block text-sm font-bold mb-2" style="color: var(--color-gray)">Description (Optional)</label>
                                        <textarea name="description" id="description" rows="3"
                                                  class="form-input-modern w-full"
                                                  placeholder="Additional details...">{{ old('description') }}</textarea>
                                        @error('description')
                                            <p class="mt-1 text-sm" style="color: var(--color-primary-red)">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="last_completed_date" class="block text-sm font-bold mb-2" style="color: var(--color-gray)">Last Completed Date (Optional)</label>
                                        <input type="date" name="last_completed_date" id="last_completed_date"
                                               class="form-input-modern w-full"
                                               value="{{ old('last_completed_date') }}">
                                        @error('last_completed_date')
                                            <p class="mt-1 text-sm" style="color: var(--color-primary-red)">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="frequency_days" class="block text-sm font-bold mb-2" style="color: var(--color-gray)">Remind me every (Optional)</label>
                                        <div class="flex items-center">
                                            <input type="number" name="frequency_days" id="frequency_days" min="1" max="3650" placeholder="30"
                                                   class="form-input-modern w-24 text-center"
                                                   value="{{ old('frequency_days') }}">
                                            <span class="ml-2 text-sm font-medium" style="color: var(--color-gray)">days</span>
                                        </div>
                                        @error('frequency_days')
                                            <p class="mt-1 text-sm" style="color: var(--color-primary-red)">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div class="pt-4">
                                        <button type="submit" class="btn-primary w-full flex items-center justify-center">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                            </svg>
                                            Add Activity
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript for inline editing -->
    <script>
        function updateLastCompleted(input) {
            const activityId = input.dataset.activityId;
            const date = input.value;
            
            if (!date) {
                alert('Please select a date');
                return;
            }

            fetch(`/activity-trackers/${activityId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    last_completed_date: date
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Refresh the page to show updated "time ago" text
                    location.reload();
                } else {
                    alert('Error updating date');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error updating date');
            });
        }

        function updateFrequency(input) {
            const activityId = input.dataset.activityId;
            const frequency = input.value;

            fetch(`/activity-trackers/${activityId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    frequency_days: frequency || null
                })
            })
            .then(response => response.json())
            .then(data => {
                if (!data.success) {
                    alert('Error updating frequency');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error updating frequency');
            });
        }

        function editActivity(activityId, field) {
            const container = document.getElementById(`activity-${activityId}`);
            
            if (field === 'name') {
                const displayElement = container.querySelector('.name-display');
                const editElement = container.querySelector('.name-edit');
                
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

        function saveActivity(activityId, field, value) {
            // Don't save if value is empty for name field
            if (field === 'name' && !value.trim()) {
                alert('Activity name cannot be empty');
                return;
            }

            const data = {};
            data[field] = value.trim();

            fetch(`/activity-trackers/${activityId}`, {
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
                    alert('Error saving activity');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error saving activity');
            });
        }

        function handleEditKeydown(event, activityId, field, value) {
            if (event.key === 'Enter' && !event.shiftKey) {
                event.preventDefault();
                saveActivity(activityId, field, value);
            } else if (event.key === 'Escape') {
                // Cancel edit and return to display mode
                const container = document.getElementById(`activity-${activityId}`);
                
                if (field === 'name') {
                    const displayElement = container.querySelector('.name-display');
                    const editElement = container.querySelector('.name-edit');
                    
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