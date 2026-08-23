<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CarpoolSchedule;
use App\Notifications\CarpoolScheduleApproved;
use App\Notifications\CarpoolScheduleRejected;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class CarpoolScheduleController extends Controller
{
    protected function ensurePermission(Request $request): void
    {
        abort_unless($request->user()->hasPermission('manage-carpooling'), 403);
    }

    public function pending(Request $request): View
    {
        $this->ensurePermission($request);

        $schedules = CarpoolSchedule::pending()->with(['driverProfile.user', 'car'])->latest()->paginate(15);

        return view('admin.carpooling.schedules.pending', compact('schedules'));
    }

    public function approvedIndex(Request $request): View
    {
        $this->ensurePermission($request);

        $schedules = CarpoolSchedule::approved()->with(['driverProfile.user', 'car'])->latest('approved_at')->paginate(15);

        return view('admin.carpooling.schedules.approved', compact('schedules'));
    }

    public function rejectedIndex(Request $request): View
    {
        $this->ensurePermission($request);

        $schedules = CarpoolSchedule::rejected()->with(['driverProfile.user', 'car'])->latest()->paginate(15);

        return view('admin.carpooling.schedules.rejected', compact('schedules'));
    }

    public function show(Request $request, CarpoolSchedule $carpoolSchedule): View
    {
        $this->ensurePermission($request);

        $carpoolSchedule->load(['driverProfile.user', 'car', 'approver', 'bookings.passenger']);

        return view('admin.carpooling.schedules.show', ['schedule' => $carpoolSchedule]);
    }

    public function approve(Request $request, CarpoolSchedule $carpoolSchedule): RedirectResponse
    {
        $this->ensurePermission($request);

        $carpoolSchedule->update([
            'status' => CarpoolSchedule::STATUS_APPROVED,
            'approved_by' => $request->user()->id,
            'approved_at' => now(),
            'rejection_reason' => null,
        ]);

        $carpoolSchedule->driverProfile->user->notify(new CarpoolScheduleApproved($carpoolSchedule));

        AuditLogger::log('approved_carpool_schedule', $carpoolSchedule, "Approved carpool trip \"{$carpoolSchedule->origin} to {$carpoolSchedule->destination}\".");
        Cache::forget('homepage.content');

        return back()->with('status', 'Trip approved.');
    }

    public function reject(Request $request, CarpoolSchedule $carpoolSchedule): RedirectResponse
    {
        $this->ensurePermission($request);

        $data = $request->validate(['rejection_reason' => ['required', 'string', 'max:1000']]);

        $carpoolSchedule->update([
            'status' => CarpoolSchedule::STATUS_REJECTED,
            'rejection_reason' => $data['rejection_reason'],
            'approved_by' => null,
            'approved_at' => null,
        ]);

        $carpoolSchedule->driverProfile->user->notify(new CarpoolScheduleRejected($carpoolSchedule));

        AuditLogger::log('rejected_carpool_schedule', $carpoolSchedule, "Rejected carpool trip \"{$carpoolSchedule->origin} to {$carpoolSchedule->destination}\".");

        return redirect()->route('admin.carpooling.schedules.pending')->with('status', 'Trip rejected.');
    }
}
