<?php

namespace App\Http\Controllers;

use App\Models\MentorProfile;
use App\Models\Mentorship;
use App\Models\MentorshipRequest;
use App\Models\User;
use App\Notifications\MentorshipRequestReceived;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MentorshipController extends Controller
{
    public function index(Request $request): View
    {
        $mentors = MentorProfile::active()
            ->with('user')
            ->when($request->filled('expertise'), fn ($q) => $q->where('expertise', 'like', '%' . $request->string('expertise') . '%'))
            ->when($request->filled('industry'), fn ($q) => $q->where('industry', 'like', '%' . $request->string('industry') . '%'))
            ->where('user_id', '!=', $request->user()->id)
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $myMentorProfile = $request->user()->mentorProfile;
        $requestedMentorIds = MentorshipRequest::where('mentee_id', $request->user()->id)->pluck('mentor_id');

        return view('mentorship.index', compact('mentors', 'myMentorProfile', 'requestedMentorIds'));
    }

    public function becomeMentor(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'expertise' => ['required', 'string', 'max:255'],
            'industry' => ['nullable', 'string', 'max:150'],
            'experience_years' => ['nullable', 'integer', 'min:0', 'max:60'],
            'availability' => ['nullable', 'string', 'max:150'],
            'topics' => ['nullable', 'string', 'max:1000'],
            'bio' => ['nullable', 'string', 'max:2000'],
        ]);

        MentorProfile::updateOrCreate(['user_id' => $request->user()->id], $data + ['is_active' => true]);

        return back()->with('status', 'Your mentor profile is live. Mentees can now find and reach out to you.');
    }

    public function toggleActive(Request $request): RedirectResponse
    {
        $profile = $request->user()->mentorProfile;
        abort_unless($profile, 404);

        $profile->update(['is_active' => ! $profile->is_active]);

        return back()->with('status', $profile->is_active ? 'You are now listed as an active mentor.' : 'You are no longer listed as an active mentor.');
    }

    public function requestMentorship(Request $request, MentorProfile $mentorProfile): RedirectResponse
    {
        abort_if($request->user()->id === $mentorProfile->user_id, 422);

        $data = $request->validate(['message' => ['nullable', 'string', 'max:1000']]);

        $mentorshipRequest = MentorshipRequest::firstOrCreate(
            ['mentee_id' => $request->user()->id, 'mentor_id' => $mentorProfile->user_id],
            ['message' => $data['message'] ?? null, 'status' => MentorshipRequest::STATUS_PENDING]
        );

        User::find($mentorProfile->user_id)?->notify(new MentorshipRequestReceived($mentorshipRequest));

        return back()->with('status', 'Your mentorship request has been sent.');
    }

    public function myMentorships(Request $request): View
    {
        $user = $request->user();

        $receivedRequests = MentorshipRequest::with('mentee')->where('mentor_id', $user->id)->where('status', 'pending')->latest()->get();
        $sentRequests = MentorshipRequest::with('mentor')->where('mentee_id', $user->id)->latest()->get();
        $activeAsMentor = Mentorship::active()->with('mentee')->where('mentor_id', $user->id)->get();
        $activeAsMentee = Mentorship::active()->with('mentor')->where('mentee_id', $user->id)->get();

        return view('mentorship.mine', compact('receivedRequests', 'sentRequests', 'activeAsMentor', 'activeAsMentee'));
    }

    public function accept(Request $request, MentorshipRequest $mentorshipRequest): RedirectResponse
    {
        abort_unless($mentorshipRequest->mentor_id === $request->user()->id, 403);

        $mentorshipRequest->update(['status' => MentorshipRequest::STATUS_ACCEPTED, 'responded_at' => now()]);

        Mentorship::create([
            'mentorship_request_id' => $mentorshipRequest->id,
            'mentor_id' => $mentorshipRequest->mentor_id,
            'mentee_id' => $mentorshipRequest->mentee_id,
            'started_at' => now(),
            'status' => Mentorship::STATUS_ACTIVE,
        ]);

        return back()->with('status', 'Mentorship request accepted.');
    }

    public function reject(Request $request, MentorshipRequest $mentorshipRequest): RedirectResponse
    {
        abort_unless($mentorshipRequest->mentor_id === $request->user()->id, 403);

        $mentorshipRequest->update(['status' => MentorshipRequest::STATUS_REJECTED, 'responded_at' => now()]);

        return back()->with('status', 'Mentorship request declined.');
    }
}
