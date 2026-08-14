<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use App\Notifications\AdminAnnouncementPosted;
use App\Notifications\ProfileVerified;
use App\Services\AuditLogger;
use App\Services\ProfileCompletionCalculator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AlumniController extends Controller
{
    protected function ensurePermission(Request $request): void
    {
        abort_unless($request->user()->hasPermission('manage-alumni'), 403);
    }

    protected function baseQuery(Request $request)
    {
        return User::query()
            ->whereHas('role', fn ($q) => $q->where('slug', Role::ALUMNI))
            ->with('alumniProfile.department')
            ->when($request->filled('search'), function ($q) use ($request) {
                $term = $request->string('search');
                $q->where(fn ($w) => $w->where('first_name', 'like', "%{$term}%")
                    ->orWhere('last_name', 'like', "%{$term}%")
                    ->orWhere('email', 'like', "%{$term}%"));
            })
            ->when($request->filled('department_id'), fn ($q) => $q->whereHas('alumniProfile', fn ($p) => $p->where('department_id', $request->integer('department_id'))));
    }

    public function index(Request $request): View
    {
        $this->ensurePermission($request);

        $alumni = $this->baseQuery($request)->latest()->paginate(20)->withQueryString();

        return view('admin.alumni.index', compact('alumni'));
    }

    public function pending(Request $request): View
    {
        $this->ensurePermission($request);

        $alumni = $this->baseQuery($request)->pending()->latest()->paginate(20)->withQueryString();

        return view('admin.alumni.pending', compact('alumni'));
    }

    public function verified(Request $request): View
    {
        $this->ensurePermission($request);

        $alumni = $this->baseQuery($request)->verified()->latest()->paginate(20)->withQueryString();

        return view('admin.alumni.verified', compact('alumni'));
    }

    public function suspended(Request $request): View
    {
        $this->ensurePermission($request);

        $alumni = $this->baseQuery($request)->suspended()->latest()->paginate(20)->withQueryString();

        return view('admin.alumni.suspended', compact('alumni'));
    }

    public function show(Request $request, User $user): View
    {
        $this->ensurePermission($request);

        $user->load([
            'alumniProfile.department', 'alumniProfile.program', 'alumniProfile.degree', 'alumniProfile.campus',
            'alumniProfile.skills', 'alumniProfile.interests',
        ]);

        return view('admin.alumni.show', ['alumnus' => $user]);
    }

    public function verify(Request $request, User $user, ProfileCompletionCalculator $calculator): RedirectResponse
    {
        $this->authorize('manageAccountStatus', $user);

        $user->update(['status' => User::STATUS_VERIFIED, 'rejection_reason' => null]);

        if ($user->alumniProfile) {
            $user->alumniProfile->update(['verified_by' => $request->user()->id, 'verified_at' => now()]);
            $calculator->refresh($user->alumniProfile);
        }

        $user->notify(new ProfileVerified());

        AuditLogger::log('verified_alumni', $user, "Verified alumni account for {$user->full_name}.");

        return back()->with('status', $user->full_name . ' has been verified.');
    }

    public function reject(Request $request, User $user): RedirectResponse
    {
        $this->authorize('manageAccountStatus', $user);

        $data = $request->validate(['rejection_reason' => ['nullable', 'string', 'max:500']]);

        $user->update(['status' => User::STATUS_REJECTED, 'rejection_reason' => $data['rejection_reason'] ?? null]);

        AuditLogger::log('rejected_alumni', $user, "Rejected alumni account for {$user->full_name}.");

        return back()->with('status', $user->full_name . ' has been rejected.');
    }

    public function suspend(Request $request, User $user): RedirectResponse
    {
        $this->authorize('manageAccountStatus', $user);

        $data = $request->validate(['rejection_reason' => ['nullable', 'string', 'max:500']]);

        $user->update(['status' => User::STATUS_SUSPENDED, 'rejection_reason' => $data['rejection_reason'] ?? null]);

        AuditLogger::log('suspended_user', $user, "Suspended user account for {$user->full_name}.");

        return back()->with('status', $user->full_name . ' has been suspended.');
    }

    public function restore(Request $request, User $user): RedirectResponse
    {
        $this->authorize('manageAccountStatus', $user);

        $user->update(['status' => User::STATUS_VERIFIED, 'rejection_reason' => null]);

        AuditLogger::log('restored_user', $user, "Restored user account for {$user->full_name}.");

        return back()->with('status', $user->full_name . ' has been restored.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        $this->authorize('delete', $user);

        $name = $user->full_name;
        $user->delete();

        AuditLogger::log('deleted_alumni', null, "Deleted alumni account for {$name}.");

        return back()->with('status', 'Alumni account deleted.');
    }

    public function bulkAction(Request $request): RedirectResponse
    {
        $this->ensurePermission($request);

        $data = $request->validate([
            'action' => ['required', 'in:verify,suspend,notify'],
            'user_ids' => ['required', 'array'],
            'user_ids.*' => ['exists:users,id'],
        ]);

        $users = User::whereIn('id', $data['user_ids'])->whereHas('role', fn ($q) => $q->where('slug', Role::ALUMNI))->get();

        match ($data['action']) {
            'verify' => $users->each(fn ($u) => $u->update(['status' => User::STATUS_VERIFIED])),
            'suspend' => $users->each(fn ($u) => $u->update(['status' => User::STATUS_SUSPENDED])),
            'notify' => Notification::send($users, new AdminAnnouncementPosted(
                new \App\Models\Announcement(['title' => 'Update from the Alumni Office', 'body' => 'Please check your dashboard for updates.'])
            )),
        };

        return back()->with('status', 'Bulk action applied to ' . $users->count() . ' alumni.');
    }

    public function export(Request $request): StreamedResponse
    {
        $this->ensurePermission($request);

        return response()->streamDownload(function () use ($request) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Name', 'Email', 'Status', 'Student ID', 'Department', 'Graduation Year', 'Joined']);

            $this->baseQuery($request)->chunk(200, function ($chunk) use ($handle) {
                foreach ($chunk as $user) {
                    fputcsv($handle, [
                        $user->full_name,
                        $user->email,
                        $user->status,
                        $user->alumniProfile?->student_id,
                        $user->alumniProfile?->department?->name,
                        $user->alumniProfile?->graduation_year,
                        $user->created_at->format('Y-m-d'),
                    ]);
                }
            });

            fclose($handle);
        }, 'alumni-export.csv', ['Content-Type' => 'text/csv']);
    }
}
