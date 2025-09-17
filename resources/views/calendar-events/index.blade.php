<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-white leading-tight flex items-center">
            <svg class="w-9 h-9 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            {{ __('Calendar') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-medium">
                            {{ \Carbon\Carbon::create($year, $month, 1)->format('F Y') }}
                        </h3>

                        <div class="flex items-center space-x-4">
                            <!-- Month Navigation -->
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('calendar-events.index', ['year' => $startOfMonth->copy()->subMonth()->year, 'month' => $startOfMonth->copy()->subMonth()->month]) }}"
                                   class="px-3 py-1 bg-gray-200 hover:bg-gray-300 rounded text-sm">
                                    ‹ Previous
                                </a>
                                <a href="{{ route('calendar-events.index', ['year' => now()->year, 'month' => now()->month]) }}"
                                   class="px-3 py-1 bg-blue-500 hover:bg-blue-600 text-white rounded text-sm">
                                    Today
                                </a>
                                <a href="{{ route('calendar-events.index', ['year' => $startOfMonth->copy()->addMonth()->year, 'month' => $startOfMonth->copy()->addMonth()->month]) }}"
                                   class="px-3 py-1 bg-gray-200 hover:bg-gray-300 rounded text-sm">
                                    Next ›
                                </a>
                            </div>

                            <!-- Jump to Month Form -->
                            <form method="GET" action="{{ route('calendar-events.index') }}" class="flex items-center space-x-2">
                                <select name="month" class="text-sm border border-gray-300 rounded px-2 py-1 w-32">
                                    @for ($m = 1; $m <= 12; $m++)
                                        <option value="{{ $m }}" {{ $m == $month ? 'selected' : '' }}>
                                            {{ \Carbon\Carbon::create(null, $m, 1)->format('F') }}
                                        </option>
                                    @endfor
                                </select>
                                <input type="number" name="year" value="{{ $year }}" min="2020" max="2030"
                                       class="text-sm border border-gray-300 rounded px-2 py-1 w-20">
                                <button type="submit" class="px-3 py-1 bg-gray-500 hover:bg-gray-600 text-white rounded text-sm">
                                    Go
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <!-- Calendar Grid -->
                        <div class="lg:col-span-2">
                            <div class="border border-gray-200 rounded-lg overflow-hidden" style="background: linear-gradient(135deg, rgba(219, 161, 79, 0.1) 0%, rgba(78, 210, 219, 0.1) 100%);">
                                <!-- Calendar Header -->
                                <div class="grid grid-cols-7">
                                    @foreach(['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $day)
                                        <div class="p-3 text-center text-sm font-medium text-gray-700 border-r border-gray-200 last:border-r-0 bg-black/10">
                                            {{ $day }}
                                        </div>
                                    @endforeach
                                </div>

                                <!-- Calendar Body -->
                                <div class="grid grid-cols-7">
                                    @php
                                        $startDate = $startOfMonth->copy()->startOfWeek(Carbon\Carbon::SUNDAY);
                                        // Calculate if we need 6 weeks: check if the last day of the month falls beyond 5 weeks
                                        $lastDayOfMonth = $endOfMonth->copy();
                                        $endOf5Weeks = $startDate->copy()->addDays(34); // 5 weeks = 35 days - 1

                                        // If the last day of the current month is beyond 5 weeks, show 6 weeks
                                        if ($lastDayOfMonth > $endOf5Weeks) {
                                            $endDate = $startDate->copy()->addDays(41); // 6 weeks = 42 days - 1
                                        } else {
                                            $endDate = $endOf5Weeks; // 5 weeks
                                        }

                                        $currentDate = $startDate->copy();
                                    @endphp

                                    @while ($currentDate <= $endDate)
                                        @php
                                            $dateStr = $currentDate->format('Y-m-d');
                                            $eventCount = isset($events[$dateStr]) ? $events[$dateStr]->count() : 0;
                                            $isCurrentMonth = $currentDate->month == $month;
                                            $isToday = $currentDate->isToday();
                                            $isSelected = $selectedDate == $dateStr;
                                        @endphp

                                        <div class="h-20 border-r border-b border-gray-200 last:border-r-0
                                                    {{ !$isCurrentMonth ? 'bg-black/5' : '' }}
                                                    cursor-pointer hover:bg-white/30
                                                    {{ $isSelected ? 'ring-2 ring-inset' : '' }}
                                                    {{ $isSelected ? ($isToday ? 'bg-blue-100 ring-blue-400' : 'bg-yellow-100 ring-yellow-400') : '' }}"
                                             onclick="selectDate('{{ $dateStr }}')"
                                             style="{{ $eventCount > 0 && !$isToday ? 'background: linear-gradient(135deg, var(--color-yellow-light) 0%, var(--color-blue-light) 100%); box-shadow: inset 0 0 0 2px var(--color-primary-yellow);' : '' }}">
                                            <div class="p-2 h-full flex flex-col">
                                                <div class="flex justify-between items-start">
                                                    <span class="text-sm {{ $isCurrentMonth ? 'text-gray-900' : 'text-gray-400' }}
                                                                 {{ $isToday ? 'font-bold text-white rounded-full w-6 h-6 flex items-center justify-center text-xs' : '' }}"
                                                          style="{{ $isToday ? 'background: var(--color-primary-blue);' : '' }}">
                                                        {{ $currentDate->day }}
                                                    </span>
                                                    @if ($eventCount > 0)
                                                        <span class="text-white text-xs rounded-full w-5 h-5 flex items-center justify-center font-medium"
                                                              style="background: var(--color-primary-red);">
                                                            {{ $eventCount }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        @php $currentDate->addDay(); @endphp
                                    @endwhile
                                </div>
                            </div>
                        </div>

                        <!-- Selected Date Panel -->
                        <div class="lg:col-span-1">
                            <div class="border border-gray-200 rounded-lg p-4">
                                <h4 class="font-medium text-gray-900 mb-4">
                                    @if ($selectedDate)
                                        Events for {{ \Carbon\Carbon::parse($selectedDate)->format('M j, Y') }}
                                    @else
                                        Select a date to view events
                                    @endif
                                </h4>

                                <div id="selected-events" class="space-y-3">
                                    @if ($selectedDate && $selectedEvents->count() > 0)
                                        @foreach ($selectedEvents as $event)
                                            <div class="bg-gray-50 rounded p-3 border">
                                                <div class="flex justify-between items-start">
                                                    <div class="flex-1">
                                                        <h5 class="font-medium text-gray-900">{{ $event->title }}</h5>
                                                        <p class="text-sm text-gray-600">{{ \Carbon\Carbon::parse($event->time)->format('g:i A') }}</p>
                                                        @if ($event->description)
                                                            <p class="text-sm text-gray-700 mt-1">{{ $event->description }}</p>
                                                        @endif
                                                    </div>
                                                    <div class="flex items-center space-x-2 ml-2">
                                                        <button onclick="editEvent({{ $event->id }}, '{{ $event->title }}', '{{ $event->description }}', '{{ $event->date->format('Y-m-d') }}', '{{ \Carbon\Carbon::parse($event->time)->format('H:i') }}')"
                                                                class="text-blue-600 hover:text-blue-800 text-xs px-2 py-1 hover:bg-blue-50 rounded">
                                                            Edit
                                                        </button>
                                                        <form method="POST" action="{{ route('calendar-events.destroy', $event) }}" class="inline flex"
                                                              onsubmit="return confirm('Are you sure you want to delete this event?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="text-red-600 hover:text-red-800 text-xs px-2 py-1 hover:bg-red-50 rounded">
                                                                Delete
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @elseif ($selectedDate)
                                        <p class="text-gray-500 text-sm">No events on this date.</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Add New Event Form -->
                    <div class="mt-8 border border-gray-200 rounded-lg p-6">
                        <h4 class="font-medium text-gray-900 mb-4">Add New Event</h4>

                        <form id="event-form" method="POST" action="{{ route('calendar-events.store') }}">
                            @csrf
                            <input type="hidden" id="form-method" name="_method" value="">
                            <input type="hidden" id="form-event-id" name="event_id" value="">

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                                    <input type="text" name="title" id="title" required
                                           class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                           value="{{ old('title') }}">
                                    @error('title')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="date" class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                                    <input type="date" name="date" id="date" required
                                           class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                           value="{{ old('date', $selectedDate ?: now()->format('Y-m-d')) }}">
                                    @error('date')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="time" class="block text-sm font-medium text-gray-700 mb-1">Time</label>
                                    <input type="time" name="time" id="time" required
                                           class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                           value="{{ old('time', '13:00') }}">
                                    @error('time')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description (Optional)</label>
                                    <textarea name="description" id="description" rows="2"
                                              class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('description') }}</textarea>
                                    @error('description')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="flex space-x-3 mt-4">
                                <button type="submit" id="form-submit-btn"
                                        class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-md">
                                    Add Event
                                </button>
                                <button type="button" id="cancel-edit-btn" onclick="cancelEdit()"
                                        class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-700 rounded-md hidden">
                                    Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function selectDate(date) {
            const url = new URL(window.location);
            url.searchParams.set('date', date);
            window.location.href = url.toString();
        }

        function editEvent(id, title, description, date, time) {
            const form = document.getElementById('event-form');
            const methodInput = document.getElementById('form-method');
            const eventIdInput = document.getElementById('form-event-id');
            const submitBtn = document.getElementById('form-submit-btn');
            const cancelBtn = document.getElementById('cancel-edit-btn');

            // Update form for editing
            form.action = `/calendar/${id}`;
            methodInput.value = 'PUT';
            eventIdInput.value = id;

            // Fill form fields
            document.getElementById('title').value = title;
            document.getElementById('description').value = description || '';
            document.getElementById('date').value = date;
            document.getElementById('time').value = time;

            // Update button text and show cancel
            submitBtn.textContent = 'Update Event';
            submitBtn.className = 'px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-md';
            cancelBtn.classList.remove('hidden');

            // Scroll to form
            form.scrollIntoView({ behavior: 'smooth' });
        }

        function cancelEdit() {
            const form = document.getElementById('event-form');
            const methodInput = document.getElementById('form-method');
            const eventIdInput = document.getElementById('form-event-id');
            const submitBtn = document.getElementById('form-submit-btn');
            const cancelBtn = document.getElementById('cancel-edit-btn');

            // Reset form for adding
            form.action = '{{ route('calendar-events.store') }}';
            methodInput.value = '';
            eventIdInput.value = '';

            // Clear form fields
            document.getElementById('title').value = '';
            document.getElementById('description').value = '';
            document.getElementById('date').value = '{{ $selectedDate ?: now()->format('Y-m-d') }}';
            document.getElementById('time').value = '13:00';

            // Update button text and hide cancel
            submitBtn.textContent = 'Add Event';
            submitBtn.className = 'px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-md';
            cancelBtn.classList.add('hidden');
        }
    </script>
</x-app-layout>