<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MentorProfile;
use App\Models\MentorshipRequest;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MentorshipController extends Controller
{
    protected function ensurePermission(Request $request): void
    {
        abort_unless($request->user()->hasPermission('manage-mentorship'), 403);
    }

    public function mentors(Request $request): View
    {
        $this->ensurePermission($request);

        $mentors = MentorProfile::with('user')->latest()->paginate(20);

        return view('admin.mentorship.mentors', compact('mentors'));
    }

    public function requests(Request $request): View
    {
        $this->ensurePermission($request);

        $requests = MentorshipRequest::with(['mentor', 'mentee', 'mentorship'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.mentorship.requests', compact('requests'));
    }

    public function approve(Request $request, MentorshipRequest $mentorshipRequest): RedirectResponse
    {
        $this->ensurePermission($request);

        $mentorshipRequest->update([
            'admin_status' => MentorshipRequest::ADMIN_STATUS_APPROVED,
            'admin_reviewed_by' => $request->user()->id,
            'admin_reviewed_at' => now(),
        ]);
        $mentorshipRequest->activateIfFullyApproved();

        AuditLogger::log('approved_mentorship_request', $mentorshipRequest, "Approved mentorship request #{$mentorshipRequest->id}.");

        $message = $mentorshipRequest->isFullyApproved()
            ? 'Mentorship request approved. The mentorship is now active.'
            : 'Mentorship request approved. It will become active once the mentor also accepts it.';

        return back()->with('status', $message);
    }

    public function reject(Request $request, MentorshipRequest $mentorshipRequest): RedirectResponse
    {
        $this->ensurePermission($request);

        $mentorshipRequest->update([
            'admin_status' => MentorshipRequest::ADMIN_STATUS_REJECTED,
            'admin_reviewed_by' => $request->user()->id,
            'admin_reviewed_at' => now(),
        ]);

        AuditLogger::log('rejected_mentorship_request', $mentorshipRequest, "Rejected mentorship request #{$mentorshipRequest->id}.");

        return back()->with('status', 'Mentorship request rejected.');
    }
}
