<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CarpoolDriverProfile;
use App\Notifications\CarpoolDriverApproved;
use App\Notifications\CarpoolDriverRejected;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CarpoolDriverController extends Controller
{
    protected function ensurePermission(Request $request): void
    {
        abort_unless($request->user()->hasPermission('manage-carpooling'), 403);
    }

    public function pending(Request $request): View
    {
        $this->ensurePermission($request);

        $profiles = CarpoolDriverProfile::pending()->with('user')->latest()->paginate(15);

        return view('admin.carpooling.drivers.pending', compact('profiles'));
    }

    public function approvedIndex(Request $request): View
    {
        $this->ensurePermission($request);

        $profiles = CarpoolDriverProfile::approved()->with('user')->withCount('cars')->latest('reviewed_at')->paginate(15);

        return view('admin.carpooling.drivers.approved', compact('profiles'));
    }

    public function rejectedIndex(Request $request): View
    {
        $this->ensurePermission($request);

        $profiles = CarpoolDriverProfile::rejected()->with('user')->latest()->paginate(15);

        return view('admin.carpooling.drivers.rejected', compact('profiles'));
    }

    public function show(Request $request, CarpoolDriverProfile $carpoolDriverProfile): View
    {
        $this->ensurePermission($request);

        $carpoolDriverProfile->load(['user', 'cars', 'reviewer']);

        return view('admin.carpooling.drivers.show', ['profile' => $carpoolDriverProfile]);
    }

    public function approve(Request $request, CarpoolDriverProfile $carpoolDriverProfile): RedirectResponse
    {
        $this->ensurePermission($request);

        $carpoolDriverProfile->update([
            'status' => CarpoolDriverProfile::STATUS_APPROVED,
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
            'rejection_reason' => null,
        ]);

        $carpoolDriverProfile->user->notify(new CarpoolDriverApproved($carpoolDriverProfile));

        AuditLogger::log('approved_carpool_driver', $carpoolDriverProfile, "Approved carpool driver application for \"{$carpoolDriverProfile->user->full_name}\".");

        return back()->with('status', 'Driver approved.');
    }

    public function reject(Request $request, CarpoolDriverProfile $carpoolDriverProfile): RedirectResponse
    {
        $this->ensurePermission($request);

        $data = $request->validate(['rejection_reason' => ['required', 'string', 'max:1000']]);

        $carpoolDriverProfile->update([
            'status' => CarpoolDriverProfile::STATUS_REJECTED,
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
            'rejection_reason' => $data['rejection_reason'],
        ]);

        $carpoolDriverProfile->user->notify(new CarpoolDriverRejected($carpoolDriverProfile));

        AuditLogger::log('rejected_carpool_driver', $carpoolDriverProfile, "Rejected carpool driver application for \"{$carpoolDriverProfile->user->full_name}\".");

        return redirect()->route('admin.carpooling.drivers.pending')->with('status', 'Driver application rejected.');
    }

    public function suspend(Request $request, CarpoolDriverProfile $carpoolDriverProfile): RedirectResponse
    {
        $this->ensurePermission($request);

        $data = $request->validate(['rejection_reason' => ['required', 'string', 'max:1000']]);

        $carpoolDriverProfile->update([
            'status' => CarpoolDriverProfile::STATUS_SUSPENDED,
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
            'rejection_reason' => $data['rejection_reason'],
        ]);

        AuditLogger::log('suspended_carpool_driver', $carpoolDriverProfile, "Suspended carpool driver \"{$carpoolDriverProfile->user->full_name}\".");

        return back()->with('status', 'Driver suspended.');
    }
}
