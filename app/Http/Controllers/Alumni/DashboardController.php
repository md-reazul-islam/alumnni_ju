<?php

namespace App\Http\Controllers\Alumni;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\AlumniProfile;
use App\Models\Connection;
use App\Models\Donation;
use App\Models\Event;
use App\Models\JobPosting;
use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $profile = $user->alumniProfile;

        $stats = [
            'profile_views' => $profile?->views ?? 0,
            'connections' => Connection::accepted()
                ->where(fn ($q) => $q->where('requester_id', $user->id)->orWhere('recipient_id', $user->id))
                ->count(),
            'events_registered' => $user->eventRegistrations()->where('status', 'registered')->count(),
            'jobs_posted' => $user->jobPostings()->count(),
            'donations' => Donation::completed()->where('donor_id', $user->id)->sum('amount'),
            'conversations' => $user->conversations()->count(),
        ];

        $upcomingEvents = Event::published()
            ->upcoming()
            ->whereHas('registrations', fn ($q) => $q->where('user_id', $user->id)->where('status', 'registered'))
            ->orderBy('event_date')
            ->limit(3)
            ->get();

        if ($upcomingEvents->isEmpty()) {
            $upcomingEvents = Event::published()->upcoming()->orderBy('event_date')->limit(3)->get();
        }

        $recommendedAlumni = AlumniProfile::with(['user', 'department', 'degree'])
            ->whereNotNull('verified_at')
            ->visibleToAlumni()
            ->where('user_id', '!=', $user->id)
            ->when($profile?->department_id, fn ($q) => $q->where('department_id', $profile->department_id))
            ->inRandomOrder()
            ->limit(4)
            ->get();

        $jobs = JobPosting::approved()->with('company')->latest('approved_at')->limit(4)->get();
        $news = News::published()->latest('published_at')->limit(3)->get();
        $announcements = Announcement::active()->where(fn ($q) => $q->where('audience', 'all')->orWhere('audience', 'alumni'))
            ->orderByDesc('is_pinned')
            ->latest('published_at')
            ->limit(3)
            ->get();

        return view('alumni.dashboard', compact(
            'user', 'profile', 'stats', 'upcomingEvents', 'recommendedAlumni', 'jobs', 'news', 'announcements'
        ));
    }
}
