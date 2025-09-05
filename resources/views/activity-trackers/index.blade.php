<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Activity Tracker') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <!-- Back to Todos Link -->
                    <div class="mb-6">
                        <a href="{{ route('todos.index') }}" class="text-blue-600 hover:text-blue-800">
                            ← Back to Today's Todos
                        </a>
                    </div>

                    <!-- Success Message -->
                    @if(session('success'))
                        <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        <!-- Activities List -->
                        <div class="lg:col-span-2">
                            <h3 class="text-lg font-medium mb-6">Activity Tracker</h3>
                            
                            <div class="space-y-4">
                                @if($activities->count() > 0)
                                    @foreach($activities as $activity)
                                        <div class="border rounded-lg p-4 bg-gray-50">
                                            <div class="flex items-start gap-4">
                                                <!-- Date Section (Left) -->
                                                <div class="w-48 flex-shrink-0">
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                                        Last completed:
                                                    </label>
                                                    <input 
                                                        type="date" 
                                                        value="{{ $activity->last_completed_date ? $activity->last_completed_date->format('Y-m-d') : '' }}"
                                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                                        data-activity-id="{{ $activity->id }}"
                                                        onchange="updateLastCompleted(this)"
                                                    />
                                                    @if($activity->last_completed_date)
                                                        <p class="text-xs text-gray-500 mt-1">
                                                            {{ \Carbon\Carbon::parse($activity->last_completed_date)->diffForHumans() }}
                                                        </p>
                                                    @endif
                                                    
                                                    <div class="mt-3">
                                                        <div class="flex items-center">
                                                            <label class="text-sm font-medium text-gray-700 mr-2">
                                                                Remind every
                                                            </label>
                                                            <input 
                                                                type="number" 
                                                                value="{{ $activity->frequency_days ?? '' }}"
                                                                min="1" 
                                                                max="3650"
                                                                placeholder="30"
                                                                class="block w-16 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                                                data-activity-id="{{ $activity->id }}"
                                                                onchange="updateFrequency(this)"
                                                            />
                                                            <span class="ml-1 text-sm text-gray-600">days</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <!-- Activity Info Section (Right) -->
                                                <div class="flex-1 flex items-start justify-between">
                                                    <div class="flex-1" id="activity-{{ $activity->id }}">
                                                        <!-- Name Section -->
                                                        <div class="name-section">
                                                            <!-- Name Display -->
                                                            <h4 class="name-display font-medium text-gray-900 cursor-pointer hover:bg-gray-100 rounded px-2 py-1 -mx-2 -my-1 transition-colors" 
                                                                onclick="editActivity({{ $activity->id }}, 'name')">
                                                                {{ $activity->name }}
                                                            </h4>
                                                            <!-- Name Edit (hidden by default) -->
                                                            <input type="text" 
                                                                   class="name-edit font-medium text-gray-900 w-full border-0 border-b-2 border-gray-300 focus:border-indigo-500 focus:ring-0 bg-transparent px-0 py-1 hidden" 
                                                                   value="{{ $activity->name }}"
                                                                   onblur="saveActivity({{ $activity->id }}, 'name', this.value)"
                                                                   onkeydown="handleEditKeydown(event, {{ $activity->id }}, 'name', this.value)">
                                                                   
                                                            <!-- Due Status Badge -->
                                                            @if($activity->days_until_due !== null)
                                                                <div class="mt-1">
                                                                    @if($activity->days_until_due < 0)
                                                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                                            Overdue by {{ abs($activity->days_until_due) }} day{{ abs($activity->days_until_due) === 1 ? '' : 's' }}
                                                                        </span>
                                                                    @elseif($activity->days_until_due === 0)
                                                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                                            Due today
                                                                        </span>
                                                                    @elseif($activity->days_until_due <= 3)
                                                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                                                            Due in {{ $activity->days_until_due }} day{{ $activity->days_until_due === 1 ? '' : 's' }}
                                                                        </span>
                                                                    @else
                                                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                                            Due in {{ $activity->days_until_due }} day{{ $activity->days_until_due === 1 ? '' : 's' }}
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                            @endif
                                                        </div>
                                                        
                                                        <!-- Description Section -->
                                                        <div class="description-section mt-1">
                                                            @if($activity->description)
                                                                <!-- Description Display -->
                                                                <p class="description-display text-gray-600 cursor-pointer hover:bg-gray-100 rounded px-2 py-1 -mx-2 transition-colors" 
                                                                   onclick="editActivity({{ $activity->id }}, 'description')">
                                                                    {{ $activity->description }}
                                                                </p>
                                                            @else
                                                                <!-- Empty Description Display -->
                                                                <p class="description-display text-gray-400 cursor-pointer hover:bg-gray-100 rounded px-2 py-1 -mx-2 italic transition-colors" 
                                                                   onclick="editActivity({{ $activity->id }}, 'description')">
                                                                    Click to add description
                                                                </p>
                                                            @endif
                                                            <!-- Description Edit (hidden by default) -->
                                                            <textarea class="description-edit text-gray-600 w-full border-0 border-b-2 border-gray-300 focus:border-indigo-500 focus:ring-0 bg-transparent px-0 py-1 resize-none hidden" 
                                                                      rows="2"
                                                                      placeholder="Enter description (optional)"
                                                                      onblur="saveActivity({{ $activity->id }}, 'description', this.value)"
                                                                      onkeydown="handleEditKeydown(event, {{ $activity->id }}, 'description', this.value)">{{ $activity->description ?? '' }}</textarea>
                                                        </div>
                                                    </div>
                                                    <form method="POST" action="{{ route('activity-trackers.destroy', $activity) }}" class="ml-4">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-600 hover:text-red-800" onclick="return confirm('Are you sure?')">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                            </svg>
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <p class="text-gray-500 text-center py-8">No activities tracked yet. Add one using the form on the right.</p>
                                @endif
                            </div>
                        </div>

                        <!-- Add Activity Form -->
                        <div>
                            <h3 class="text-lg font-medium mb-6">Add New Activity</h3>
                            
                            <form method="POST" action="{{ route('activity-trackers.store') }}" class="space-y-4">
                                @csrf
                                
                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-700">Activity Name</label>
                                    <input type="text" name="name" id="name" required
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                           placeholder="e.g., Refill cat's water"
                                           value="{{ old('name') }}">
                                    @error('name')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="description" class="block text-sm font-medium text-gray-700">Description (Optional)</label>
                                    <textarea name="description" id="description" rows="3"
                                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                              placeholder="Additional details...">{{ old('description') }}</textarea>
                                    @error('description')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="last_completed_date" class="block text-sm font-medium text-gray-700">Last Completed Date (Optional)</label>
                                    <input type="date" name="last_completed_date" id="last_completed_date"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                           value="{{ old('last_completed_date') }}">
                                    @error('last_completed_date')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="frequency_days" class="block text-sm font-medium text-gray-700">Remind me every (Optional)</label>
                                    <div class="flex items-center mt-1">
                                        <input type="number" name="frequency_days" id="frequency_days" min="1" max="3650" placeholder="30"
                                               class="block w-20 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                               value="{{ old('frequency_days') }}">
                                        <span class="ml-2 text-sm text-gray-600">days</span>
                                    </div>
                                    @error('frequency_days')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <button type="submit" 
                                        class="w-full bg-indigo-600 border border-transparent rounded-md py-2 px-4 inline-flex justify-center text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Add Activity
                                </button>
                            </form>
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