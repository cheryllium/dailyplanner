<?php

namespace App\Http\Controllers;

use App\Models\CalendarEvent;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CalendarEventController extends Controller
{
    use AuthorizesRequests;
    public function index(Request $request)
    {
        $year = $request->get('year', now()->year);
        $month = $request->get('month', now()->month);
        $selectedDate = $request->get('date');

        $startOfMonth = Carbon::create($year, $month, 1);
        $endOfMonth = $startOfMonth->copy()->endOfMonth();

        $events = Auth::user()->calendarEvents()
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->orderBy('date')
            ->orderBy('time')
            ->get()
            ->groupBy(function ($event) {
                return $event->date->format('Y-m-d');
            });

        $selectedEvents = [];
        if ($selectedDate) {
            $selectedEvents = Auth::user()->calendarEvents()
                ->whereDate('date', $selectedDate)
                ->orderBy('time')
                ->get();
        }

        return view('calendar-events.index', compact(
            'year',
            'month',
            'events',
            'startOfMonth',
            'endOfMonth',
            'selectedDate',
            'selectedEvents'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date' => 'required|date',
            'time' => 'required|date_format:H:i',
        ]);

        Auth::user()->calendarEvents()->create($request->all());

        return redirect()->back()->with('success', 'Calendar event created successfully!');
    }

    public function update(Request $request, CalendarEvent $calendarEvent)
    {
        $this->authorize('update', $calendarEvent);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date' => 'required|date',
            'time' => 'required|date_format:H:i',
        ]);

        $calendarEvent->update($request->all());

        return redirect()->back()->with('success', 'Calendar event updated successfully!');
    }

    public function destroy(CalendarEvent $calendarEvent)
    {
        $this->authorize('delete', $calendarEvent);

        $calendarEvent->delete();

        return redirect()->back()->with('success', 'Calendar event deleted successfully!');
    }

    public function getEvents(Request $request)
    {
        $date = $request->get('date');

        $events = Auth::user()->calendarEvents()
            ->whereDate('date', $date)
            ->orderBy('time')
            ->get();

        return response()->json($events);
    }
}
