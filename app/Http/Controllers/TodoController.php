<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use App\Models\RecurringTodo;
use App\Models\ActivityTracker;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class TodoController extends Controller
{
    use AuthorizesRequests;
    
    public function index()
    {
        $today = now()->format('Y-m-d');
        $user = Auth::user();
        
        // Check if we need to generate daily todos for this user
        $this->generateDailyTodosIfNeeded($user, $today);
        
        $todos = $user->todos()
            ->whereDate('date', $today)
            ->orderBy('created_at')
            ->get();

        // Get overdue activity reminders
        $overdueActivities = $user->activityTrackers()
            ->whereNotNull('frequency_days')
            ->whereNotNull('last_completed_date')
            ->get()
            ->filter(function ($activity) {
                return $activity->isOverdue();
            });

        return view('todos.index', compact('todos', 'today', 'overdueActivities'));
    }
    
    /**
     * Generate daily todos from recurring todos if needed
     */
    private function generateDailyTodosIfNeeded($user, $date)
    {
        // Use cache to track the last generation date per user to avoid multiple generations
        $cacheKey = "daily_todos_generated_user_{$user->id}";
        $lastGenerated = Cache::get($cacheKey);
        
        // If we haven't generated for today yet, generate them
        if ($lastGenerated !== $date) {
            $dayOfWeek = strtolower(Carbon::parse($date)->format('l'));
            
            // Get active recurring todos for this day
            $recurringTodos = $user->recurringTodos()
                ->where('day_of_week', $dayOfWeek)
                ->where('is_active', true)
                ->get();
            
            foreach ($recurringTodos as $recurringTodo) {
                // Check if we already have a todo for this recurring todo today
                $existingTodo = $user->todos()
                    ->whereDate('date', $date)
                    ->where('recurring_todo_id', $recurringTodo->id)
                    ->first();
                
                // Only create if it doesn't exist
                if (!$existingTodo) {
                    $user->todos()->create([
                        'title' => $recurringTodo->title,
                        'description' => $recurringTodo->description,
                        'date' => $date,
                        'recurring_todo_id' => $recurringTodo->id,
                        'is_completed' => false,
                    ]);
                }
            }
            
            // Mark that we've generated todos for this date
            Cache::put($cacheKey, $date, now()->endOfDay());
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        Auth::user()->todos()->create([
            'title' => $request->title,
            'description' => $request->description,
            'date' => now()->format('Y-m-d'),
            'is_completed' => false,
        ]);

        return redirect()->route('todos.index')->with('success', 'Todo added successfully!');
    }

    public function update(Request $request, Todo $todo)
    {
        $this->authorize('update', $todo);

        $request->validate([
            'is_completed' => 'boolean',
        ]);

        $todo->update([
            'is_completed' => $request->boolean('is_completed'),
        ]);

        return redirect()->route('todos.index')->with('success', 'Todo updated successfully!');
    }

    public function destroy(Todo $todo)
    {
        $this->authorize('delete', $todo);

        $todo->delete();

        return redirect()->route('todos.index')->with('success', 'Todo deleted successfully!');
    }

    public function completeActivityReminder(Request $request, ActivityTracker $activityTracker)
    {
        $this->authorize('update', $activityTracker);

        $activityTracker->update([
            'last_completed_date' => now()->format('Y-m-d'),
        ]);

        return redirect()->route('todos.index')->with('success', 'Activity marked as completed!');
    }
}
