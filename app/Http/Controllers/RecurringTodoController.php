<?php

namespace App\Http\Controllers;

use App\Models\RecurringTodo;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RecurringTodoController extends Controller
{
    use AuthorizesRequests;
    public function index()
    {
        $recurringTodos = Auth::user()->recurringTodos()
            ->where('is_active', true)
            ->orderBy('day_of_week')
            ->get()
            ->groupBy('day_of_week');

        $daysOfWeek = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];

        return view('recurring-todos.index', compact('recurringTodos', 'daysOfWeek'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'days_of_week' => 'required|array|min:1',
            'days_of_week.*' => 'in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
        ]);

        $user = Auth::user();
        $createdCount = 0;

        // Create a recurring todo for each selected day
        foreach ($request->days_of_week as $day) {
            $user->recurringTodos()->create([
                'title' => $request->title,
                'description' => $request->description,
                'day_of_week' => $day,
                'is_active' => true,
            ]);
            $createdCount++;
        }

        $message = $createdCount === 1 
            ? 'Recurring todo added successfully!' 
            : "Recurring todo added for {$createdCount} days successfully!";

        return redirect()->route('recurring-todos.index')->with('success', $message);
    }

    public function update(Request $request, RecurringTodo $recurringTodo)
    {
        $this->authorize('update', $recurringTodo);

        // Use the same pattern as ActivityTracker - validate what's present
        $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|nullable|string',
            'day_of_week' => 'sometimes|required|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'is_active' => 'sometimes|boolean',
        ]);

        $updateData = [];
        
        if ($request->has('title')) {
            $updateData['title'] = $request->title;
        }
        
        if ($request->has('description')) {
            $updateData['description'] = $request->description;
        }
        
        if ($request->has('day_of_week')) {
            $updateData['day_of_week'] = $request->day_of_week;
        }
        
        if ($request->has('is_active')) {
            $updateData['is_active'] = $request->boolean('is_active');
        }

        $recurringTodo->update($updateData);

        return response()->json(['success' => true]);
    }

    public function destroy(RecurringTodo $recurringTodo)
    {
        $this->authorize('delete', $recurringTodo);

        $recurringTodo->delete();

        return redirect()->route('recurring-todos.index')->with('success', 'Recurring todo deleted successfully!');
    }
}
