<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MatrimonyProfile;
use App\Notifications\MatrimonyProfileApproved;
use App\Notifications\MatrimonyProfileRejected;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class MatrimonyProfileController extends Controller
{
    protected function ensurePermission(Request $request): void
    {
        abort_unless($request->user()->hasPermission('manage-matrimony'), 403);
    }

    public function pending(Request $request): View
    {
        $this->ensurePermission($request);

        $profiles = MatrimonyProfile::pending()->with('creator')->latest()->paginate(15);

        return view('admin.matrimony.profiles.pending', compact('profiles'));
    }

    public function approvedIndex(Request $request): View
    {
        $this->ensurePermission($request);

        $profiles = MatrimonyProfile::approved()->with('creator')->latest('reviewed_at')->paginate(15);

        return view('admin.matrimony.profiles.approved', compact('profiles'));
    }

    public function rejectedIndex(Request $request): View
    {
        $this->ensurePermission($request);

        $profiles = MatrimonyProfile::rejected()->with('creator')->latest()->paginate(15);

        return view('admin.matrimony.profiles.rejected', compact('profiles'));
    }

    public function suspendedIndex(Request $request): View
    {
        $this->ensurePermission($request);

        $profiles = MatrimonyProfile::suspended()->with('creator')->latest()->paginate(15);

        return view('admin.matrimony.profiles.suspended', compact('profiles'));
    }

    public function show(Request $request, MatrimonyProfile $profile): View
    {
        $this->ensurePermission($request);

        $profile->load(['creator', 'reviewer', 'photos']);

        return view('admin.matrimony.profiles.show', compact('profile'));
    }

    public function approve(Request $request, MatrimonyProfile $profile): RedirectResponse
    {
        $this->ensurePermission($request);

        $profile->update([
            'status' => MatrimonyProfile::STATUS_APPROVED,
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
            'rejection_reason' => null,
        ]);

        $profile->creator->notify(new MatrimonyProfileApproved($profile));

        AuditLogger::log('approved_matrimony_profile', $profile, "Approved matrimony profile \"{$profile->display_name}\".");
        Cache::forget('homepage.content');

        return back()->with('status', 'Profile approved.');
    }

    public function reject(Request $request, MatrimonyProfile $profile): RedirectResponse
    {
        $this->ensurePermission($request);

        $data = $request->validate(['rejection_reason' => ['required', 'string', 'max:1000']]);

        $profile->update([
            'status' => MatrimonyProfile::STATUS_REJECTED,
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
            'rejection_reason' => $data['rejection_reason'],
        ]);

        $profile->creator->notify(new MatrimonyProfileRejected($profile));

        AuditLogger::log('rejected_matrimony_profile', $profile, "Rejected matrimony profile \"{$profile->display_name}\".");

        return redirect()->route('admin.matrimony.profiles.pending')->with('status', 'Profile rejected.');
    }

    public function suspend(Request $request, MatrimonyProfile $profile): RedirectResponse
    {
        $this->ensurePermission($request);

        $data = $request->validate(['rejection_reason' => ['required', 'string', 'max:1000']]);

        $profile->update([
            'status' => MatrimonyProfile::STATUS_SUSPENDED,
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
            'rejection_reason' => $data['rejection_reason'],
        ]);

        AuditLogger::log('suspended_matrimony_profile', $profile, "Suspended matrimony profile \"{$profile->display_name}\".");
        Cache::forget('homepage.content');

        return back()->with('status', 'Profile suspended.');
    }

    public function verify(Request $request, MatrimonyProfile $profile): RedirectResponse
    {
        $this->ensurePermission($request);

        $profile->update(['is_verified' => ! $profile->is_verified]);

        AuditLogger::log(
            $profile->is_verified ? 'verified_matrimony_profile' : 'unverified_matrimony_profile',
            $profile,
            ($profile->is_verified ? 'Marked' : 'Unmarked') . " matrimony profile \"{$profile->display_name}\" as verified."
        );

        return back()->with('status', $profile->is_verified ? 'Profile marked as verified.' : 'Verification removed.');
    }
}
