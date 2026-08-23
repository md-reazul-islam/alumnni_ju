<?php

namespace App\Http\Controllers;

use App\Models\CarpoolSchedule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CarpoolScheduleController extends Controller
{
    public function create(Request $request): View
    {
        $this->authorize('create', CarpoolSchedule::class);

        $cars = $request->user()->carpoolDriverProfile->cars;
        abort_if($cars->isEmpty(), 422, 'Add a car before creating a trip schedule.');

        return view('carpooling.driver.schedules.create', compact('cars'));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', CarpoolSchedule::class);

        $data = $this->validated($request);
        $data['carpool_driver_profile_id'] = $request->user()->carpoolDriverProfile->id;
        $data['status'] = CarpoolSchedule::STATUS_PENDING;

        CarpoolSchedule::create($data);

        return redirect()->route('carpooling.driver.dashboard')->with('status', 'Trip submitted for admin approval.');
    }

    public function edit(CarpoolSchedule $schedule): View
    {
        $this->authorize('update', $schedule);

        $cars = $schedule->driverProfile->cars;

        return view('carpooling.driver.schedules.edit', compact('schedule', 'cars'));
    }

    public function update(Request $request, CarpoolSchedule $schedule): RedirectResponse
    {
        $this->authorize('update', $schedule);

        $data = $this->validated($request, $schedule);

        if ($schedule->isLocked()) {
            foreach ($this->fieldChanges($schedule, $data) as $field => $changed) {
                if ($changed) {
                    return back()
                        ->withErrors([$field => 'This trip already has paid bookings, so this field can no longer be changed.'])
                        ->withInput();
                }
            }
        }

        // Once a trip has paid bookings, editing origin/destination/notes must not pull it back to
        // pending — passengers already paid for an approved, locked trip and it must stay visible.
        $revertsToPending = $schedule->status === CarpoolSchedule::STATUS_APPROVED && ! $schedule->isLocked();

        $schedule->update($data + ($revertsToPending ? [
            'status' => CarpoolSchedule::STATUS_PENDING,
            'approved_by' => null,
            'approved_at' => null,
        ] : []));

        return redirect()->route('carpooling.driver.dashboard')->with('status', $revertsToPending
            ? 'Trip updated and sent back for admin re-approval.'
            : 'Trip updated.');
    }

    public function cancel(CarpoolSchedule $schedule): RedirectResponse
    {
        $this->authorize('update', $schedule);

        abort_if($schedule->isLocked(), 422, 'This trip has paid bookings and cannot be cancelled directly. Cancel the individual bookings instead.');

        $schedule->update(['status' => CarpoolSchedule::STATUS_CANCELLED]);

        return redirect()->route('carpooling.driver.dashboard')->with('status', 'Trip cancelled.');
    }

    protected function fieldChanges(CarpoolSchedule $schedule, array $data): array
    {
        return [
            'price_per_seat' => bccomp((string) $data['price_per_seat'], (string) $schedule->price_per_seat, 2) !== 0,
            'departure_date' => $data['departure_date'] !== $schedule->departure_date->format('Y-m-d'),
            'departure_time' => $data['departure_time'] !== substr($schedule->departure_time, 0, 5),
            'carpool_car_id' => (int) $data['carpool_car_id'] !== (int) $schedule->carpool_car_id,
        ];
    }

    protected function validated(Request $request, ?CarpoolSchedule $schedule = null): array
    {
        $profile = $request->user()->carpoolDriverProfile;

        $data = $request->validate([
            'carpool_car_id' => ['required', Rule::exists('carpool_cars', 'id')->where('carpool_driver_profile_id', $profile->id)],
            'origin' => ['required', 'string', 'max:150'],
            'destination' => ['required', 'string', 'max:150'],
            'departure_date' => ['required', 'date', 'after_or_equal:today'],
            'departure_time' => ['required', 'date_format:H:i'],
            'price_per_seat' => ['required', 'numeric', 'min:1', 'max:9999.99'],
            'seats_offered' => ['required', 'integer', 'min:1', 'max:8'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        if ($schedule) {
            $committed = $schedule->seats_booked + $schedule->heldSeats();

            if ($data['seats_offered'] < $committed) {
                throw ValidationException::withMessages([
                    'seats_offered' => "Seats offered cannot be less than the {$committed} seat(s) already booked or held.",
                ]);
            }
        }

        return $data;
    }
}
