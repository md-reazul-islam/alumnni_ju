<?php

namespace App\Http\Controllers;

use App\Models\CarpoolSchedule;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CarpoolSearchController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'date' => ['nullable', 'date'],
            'origin' => ['nullable', 'string', 'max:150'],
            'destination' => ['nullable', 'string', 'max:150'],
            'time' => ['nullable', 'date_format:H:i'],
        ]);

        $query = CarpoolSchedule::approved()
            ->upcoming()
            ->whereColumn('seats_booked', '<', 'seats_offered')
            ->with(['car', 'driverProfile.user']);

        if (! empty($filters['date'])) {
            $query->whereDate('departure_date', $filters['date']);
        }

        if (! empty($filters['origin'])) {
            $query->where('origin', 'like', '%' . $filters['origin'] . '%');
        }

        if (! empty($filters['destination'])) {
            $query->where('destination', 'like', '%' . $filters['destination'] . '%');
        }

        if (! empty($filters['time'])) {
            $query->orderByRaw('ABS(TIME_TO_SEC(departure_time) - TIME_TO_SEC(?)) asc', [$filters['time'] . ':00']);
        } else {
            $query->orderBy('departure_date')->orderBy('departure_time');
        }

        $schedules = $query->paginate(10)->withQueryString();

        return view('public.carpooling.search', [
            'schedules' => $schedules,
            'filters' => $filters,
        ]);
    }
}
