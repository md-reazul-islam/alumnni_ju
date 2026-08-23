<?php

namespace App\Http\Controllers;

use App\Models\CarpoolBooking;
use App\Models\CarpoolDriverProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CarpoolDriverController extends Controller
{
    public function become(Request $request): View
    {
        $profile = $request->user()->carpoolDriverProfile;

        return view('carpooling.driver.become', compact('profile'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'license_number' => ['required', 'string', 'max:100'],
            'license_expiry' => ['nullable', 'date'],
            'bio' => ['nullable', 'string', 'max:2000'],
        ]);

        $existing = $request->user()->carpoolDriverProfile;

        if ($existing && $existing->status !== CarpoolDriverProfile::STATUS_REJECTED) {
            $existing->update($data);

            return back()->with('status', 'Your driver profile has been updated.');
        }

        CarpoolDriverProfile::updateOrCreate(
            ['user_id' => $request->user()->id],
            $data + ['status' => CarpoolDriverProfile::STATUS_PENDING, 'rejection_reason' => null, 'reviewed_by' => null, 'reviewed_at' => null]
        );

        return back()->with('status', 'Your driver application has been submitted for review.');
    }

    public function dashboard(Request $request): View
    {
        $profile = $request->user()->carpoolDriverProfile;
        abort_unless($profile, 404);

        $profile->load(['cars', 'schedules' => fn ($q) => $q->latest('departure_date')]);

        $pendingRequestsCount = CarpoolBooking::whereIn('carpool_schedule_id', $profile->schedules->pluck('id'))
            ->where('status', CarpoolBooking::STATUS_REQUESTED)
            ->count();

        return view('carpooling.driver.dashboard', compact('profile', 'pendingRequestsCount'));
    }

    public function toggleActive(Request $request): RedirectResponse
    {
        $profile = $request->user()->carpoolDriverProfile;
        abort_unless($profile, 404);

        $profile->update(['is_active' => ! $profile->is_active]);

        return back()->with('status', $profile->is_active ? 'You are now listed as an active driver.' : 'You are no longer listed as an active driver.');
    }
}
