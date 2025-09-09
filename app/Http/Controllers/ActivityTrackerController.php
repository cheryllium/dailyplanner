<?php

namespace App\Http\Controllers;

use App\Models\ActivityTracker;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActivityTrackerController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $activities = Auth::user()->activityTrackers()
            ->get()
            ->map(function ($activity) {
                $nextDueDate = null;
                $daysUntilDue = null;
                
                if ($activity->last_completed_date && $activity->frequency_days) {
                    $nextDueDate = $activity->last_completed_date->copy()->addDays($activity->frequency_days);
                    $daysUntilDue = (int) now()->startOfDay()->diffInDays($nextDueDate->startOfDay(), false);
                }
                
                $activity->next_due_date = $nextDueDate;
                $activity->days_until_due = $daysUntilDue;
                
                return $activity;
            })
            ->sortBy(function ($activity) {
                if ($activity->days_until_due === null) {
                    return 999999;
                }
                return $activity->days_until_due;
            });

        return view('activity-trackers.index', compact('activities'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'last_completed_date' => 'nullable|date',
            'frequency_days' => 'nullable|integer|min:1|max:3650',
        ]);

        Auth::user()->activityTrackers()->create([
            'name' => $request->name,
            'description' => $request->description,
            'last_completed_date' => $request->last_completed_date,
            'frequency_days' => $request->frequency_days,
        ]);

        return redirect()->route('activity-trackers.index')
            ->with('success', 'Activity tracker created successfully.');
    }

    public function update(Request $request, ActivityTracker $activityTracker)
    {
        $this->authorize('update', $activityTracker);

        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|nullable|string',
            'last_completed_date' => 'sometimes|nullable|date',
            'frequency_days' => 'sometimes|nullable|integer|min:1|max:3650',
        ]);

        $updateData = [];
        
        if ($request->has('name')) {
            $updateData['name'] = $request->name;
        }
        
        if ($request->has('description')) {
            $updateData['description'] = $request->description;
        }
        
        if ($request->has('last_completed_date')) {
            $updateData['last_completed_date'] = $request->last_completed_date;
        }
        
        if ($request->has('frequency_days')) {
            $updateData['frequency_days'] = $request->frequency_days;
        }

        $activityTracker->update($updateData);

        return response()->json(['success' => true]);
    }

    public function destroy(ActivityTracker $activityTracker)
    {
        $this->authorize('delete', $activityTracker);

        $activityTracker->delete();

        return redirect()->route('activity-trackers.index')
            ->with('success', 'Activity tracker deleted successfully.');
    }
}
