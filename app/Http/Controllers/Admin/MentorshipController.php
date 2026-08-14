<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MentorProfile;
use App\Models\MentorshipRequest;
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

        $requests = MentorshipRequest::with(['mentor', 'mentee'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.mentorship.requests', compact('requests'));
    }
}
