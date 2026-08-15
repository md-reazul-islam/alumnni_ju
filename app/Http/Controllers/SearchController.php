<?php

namespace App\Http\Controllers;

use App\Models\AlumniProfile;
use App\Models\AlumniStory;
use App\Models\Event;
use App\Models\JobPosting;
use App\Models\News;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function search(Request $request): JsonResponse
    {
        $term = trim((string) $request->string('q'));

        if (mb_strlen($term) < 2) {
            return response()->json(['groups' => []]);
        }

        $alumni = AlumniProfile::with('user', 'department')
            ->whereNotNull('verified_at')
            ->publiclyVisible()
            ->whereHas('user', fn ($q) => $q->where(fn ($w) => $w
                ->where('first_name', 'like', "%{$term}%")
                ->orWhere('last_name', 'like', "%{$term}%")))
            ->limit(5)
            ->get()
            ->map(fn ($p) => [
                'title' => $p->user->full_name,
                'subtitle' => $p->job_title ?: $p->department?->name,
                'url' => route('alumni.profile.show', $p->user),
            ]);

        $events = Event::published()
            ->where('title', 'like', "%{$term}%")
            ->limit(5)
            ->get()
            ->map(fn ($e) => [
                'title' => $e->title,
                'subtitle' => $e->event_date->format('M d, Y'),
                'url' => route('events.show', $e),
            ]);

        $jobs = JobPosting::approved()
            ->with('company')
            ->where('title', 'like', "%{$term}%")
            ->limit(5)
            ->get()
            ->map(fn ($j) => [
                'title' => $j->title,
                'subtitle' => $j->displayCompanyName(),
                'url' => route('jobs.show', $j),
            ]);

        $news = News::published()
            ->where('title', 'like', "%{$term}%")
            ->limit(5)
            ->get()
            ->map(fn ($n) => [
                'title' => $n->title,
                'subtitle' => $n->published_at?->format('M d, Y'),
                'url' => route('news.show', $n),
            ]);

        $stories = AlumniStory::published()
            ->where('title', 'like', "%{$term}%")
            ->limit(5)
            ->get()
            ->map(fn ($s) => [
                'title' => $s->title,
                'subtitle' => 'Alumni Story',
                'url' => route('stories.show', $s),
            ]);

        $groups = collect([
            ['label' => 'Alumni', 'items' => $alumni],
            ['label' => 'Events', 'items' => $events],
            ['label' => 'Careers', 'items' => $jobs],
            ['label' => 'News', 'items' => $news],
            ['label' => 'Stories', 'items' => $stories],
        ])->filter(fn ($group) => $group['items']->isNotEmpty())->values();

        return response()->json(['groups' => $groups]);
    }
}
