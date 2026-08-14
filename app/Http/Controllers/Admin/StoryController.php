<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AlumniStory;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class StoryController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()->hasPermission('manage-stories'), 403);

        $stories = AlumniStory::query()
            ->with('alumniProfile.user')
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.stories.index', compact('stories'));
    }

    public function publish(Request $request, AlumniStory $story): RedirectResponse
    {
        $this->authorize('review', $story);

        $story->update([
            'status' => AlumniStory::STATUS_PUBLISHED,
            'reviewed_by' => $request->user()->id,
            'published_at' => now(),
        ]);

        AuditLogger::log('published_story', $story, "Published alumni story \"{$story->title}\".");
        Cache::forget('homepage.content');

        return back()->with('status', 'Story published.');
    }

    public function reject(Request $request, AlumniStory $story): RedirectResponse
    {
        $this->authorize('review', $story);

        $data = $request->validate(['rejection_reason' => ['nullable', 'string', 'max:500']]);

        $story->update([
            'status' => AlumniStory::STATUS_REJECTED,
            'reviewed_by' => $request->user()->id,
            'rejection_reason' => $data['rejection_reason'] ?? null,
        ]);

        return back()->with('status', 'Story rejected.');
    }

    public function destroy(AlumniStory $story): RedirectResponse
    {
        $this->authorize('delete', $story);

        $story->delete();

        return back()->with('status', 'Story deleted.');
    }
}
