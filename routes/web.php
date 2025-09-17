<?php

use App\Http\Controllers\ActivityTrackerController;
use App\Http\Controllers\CalendarEventController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TodoController;
use App\Http\Controllers\RecurringTodoController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('todos.index');
})->middleware(['auth']);

Route::get('/dashboard', function () {
    return redirect()->route('todos.index');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Todo routes
    Route::get('/todos', [TodoController::class, 'index'])->name('todos.index');
    Route::post('/todos', [TodoController::class, 'store'])->name('todos.store');
    Route::put('/todos/{todo}', [TodoController::class, 'update'])->name('todos.update');
    Route::delete('/todos/{todo}', [TodoController::class, 'destroy'])->name('todos.destroy');
    Route::post('/todos/complete-activity-reminder/{activityTracker}', [TodoController::class, 'completeActivityReminder'])->name('todos.complete-activity-reminder');

    // Recurring Todo routes
    Route::get('/recurring-todos', [RecurringTodoController::class, 'index'])->name('recurring-todos.index');
    Route::post('/recurring-todos', [RecurringTodoController::class, 'store'])->name('recurring-todos.store');
    Route::put('/recurring-todos/{recurringTodo}', [RecurringTodoController::class, 'update'])->name('recurring-todos.update');
    Route::delete('/recurring-todos/{recurringTodo}', [RecurringTodoController::class, 'destroy'])->name('recurring-todos.destroy');

    // Activity Tracker routes
    Route::get('/activity-trackers', [ActivityTrackerController::class, 'index'])->name('activity-trackers.index');
    Route::post('/activity-trackers', [ActivityTrackerController::class, 'store'])->name('activity-trackers.store');
    Route::put('/activity-trackers/{activityTracker}', [ActivityTrackerController::class, 'update'])->name('activity-trackers.update');
    Route::delete('/activity-trackers/{activityTracker}', [ActivityTrackerController::class, 'destroy'])->name('activity-trackers.destroy');

    // Calendar Event routes
    Route::get('/calendar', [CalendarEventController::class, 'index'])->name('calendar-events.index');
    Route::post('/calendar', [CalendarEventController::class, 'store'])->name('calendar-events.store');
    Route::put('/calendar/{calendarEvent}', [CalendarEventController::class, 'update'])->name('calendar-events.update');
    Route::delete('/calendar/{calendarEvent}', [CalendarEventController::class, 'destroy'])->name('calendar-events.destroy');
    Route::get('/calendar/events', [CalendarEventController::class, 'getEvents'])->name('calendar-events.get');
});

require __DIR__.'/auth.php';
