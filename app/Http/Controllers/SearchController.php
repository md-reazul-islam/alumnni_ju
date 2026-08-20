<?php

namespace App\Http\Controllers;

use App\Models\AlumniProfile;
use App\Models\AlumniStory;
use App\Models\Book;
use App\Models\Event;
use App\Models\JobPosting;
use App\Models\MarketplaceListing;
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
            ->where(fn ($q) => $q
                ->whereHas('user', fn ($w) => $w
                    ->where('first_name', 'like', "%{$term}%")
                    ->orWhere('last_name', 'like', "%{$term}%"))
                ->orWhere('tags', 'like', "%{$term}%"))
            ->limit(5)
            ->get()
            ->map(fn ($p) => [
                'title' => $p->user->full_name,
                'subtitle' => $p->job_title ?: $p->department?->name,
                'url' => route('alumni.profile.show', $p->user),
            ]);

        $events = Event::published()
            ->where(fn ($q) => $q->where('title', 'like', "%{$term}%")->orWhere('tags', 'like', "%{$term}%"))
            ->limit(5)
            ->get()
            ->map(fn ($e) => [
                'title' => $e->title,
                'subtitle' => $e->event_date->format('M d, Y'),
                'url' => route('events.show', $e),
            ]);

        $jobs = JobPosting::approved()
            ->with('company')
            ->where(fn ($q) => $q->where('title', 'like', "%{$term}%")->orWhere('tags', 'like', "%{$term}%"))
            ->limit(5)
            ->get()
            ->map(fn ($j) => [
                'title' => $j->title,
                'subtitle' => $j->displayCompanyName(),
                'url' => route('jobs.show', $j),
            ]);

        $news = News::published()
            ->where(fn ($q) => $q
                ->where('title', 'like', "%{$term}%")
                ->orWhereHas('tags', fn ($t) => $t->where('name', 'like', "%{$term}%")))
            ->limit(5)
            ->get()
            ->map(fn ($n) => [
                'title' => $n->title,
                'subtitle' => $n->published_at?->format('M d, Y'),
                'url' => route('news.show', $n),
            ]);

        $stories = AlumniStory::published()
            ->where(fn ($q) => $q->where('title', 'like', "%{$term}%")->orWhere('tags', 'like', "%{$term}%"))
            ->limit(5)
            ->get()
            ->map(fn ($s) => [
                'title' => $s->title,
                'subtitle' => 'Alumni Story',
                'url' => route('stories.show', $s),
            ]);

        $marketplaceListings = MarketplaceListing::approved()
            ->with('category')
            ->where(fn ($q) => $q
                ->where('title', 'like', "%{$term}%")
                ->orWhere('address', 'like', "%{$term}%")
                ->orWhere('tags', 'like', "%{$term}%"))
            ->limit(5)
            ->get()
            ->map(fn ($l) => [
                'title' => $l->title,
                'subtitle' => $l->category->name . ' · $' . number_format($l->price, 2),
                'url' => route('marketplace.show', $l),
            ]);

        $books = Book::available()
            ->where(fn ($q) => $q
                ->where('title', 'like', "%{$term}%")
                ->orWhere('author', 'like', "%{$term}%")
                ->orWhere('tags', 'like', "%{$term}%"))
            ->limit(5)
            ->get()
            ->map(fn ($b) => [
                'title' => $b->title,
                'subtitle' => $b->author,
                'url' => route('library.show', $b),
            ]);

        $groups = collect([
            ['label' => 'Alumni', 'items' => $alumni],
            ['label' => 'Events', 'items' => $events],
            ['label' => 'Careers', 'items' => $jobs],
            ['label' => 'News', 'items' => $news],
            ['label' => 'Stories', 'items' => $stories],
            ['label' => 'Marketplace', 'items' => $marketplaceListings],
            ['label' => 'Library', 'items' => $books],
        ])->filter(fn ($group) => $group['items']->isNotEmpty())->values();

        return response()->json(['groups' => $groups]);
    }
}
