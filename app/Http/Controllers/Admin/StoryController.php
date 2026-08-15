<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreStoryRequest;
use App\Models\AlumniProfile;
use App\Models\AlumniStory;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
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

    public function create(Request $request): View
    {
        abort_unless($request->user()->hasPermission('manage-stories'), 403);

        $alumniProfiles = AlumniProfile::with('user')
            ->whereNotNull('verified_at')
            ->get()
            ->sortBy(fn ($profile) => $profile->user->full_name);

        return view('admin.stories.create', compact('alumniProfiles'));
    }

    public function store(StoreStoryRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['slug'] = $this->uniqueSlug($data['title']);

        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $request->file('cover_image')->store('stories', 'public');
        }

        if ($data['status'] === AlumniStory::STATUS_PUBLISHED) {
            $data['reviewed_by'] = $request->user()->id;
            $data['published_at'] = now();
        }

        $story = AlumniStory::create($data);

        if ($story->status === AlumniStory::STATUS_PUBLISHED) {
            AuditLogger::log('published_story', $story, "Published alumni story \"{$story->title}\".");
            Cache::forget('homepage.content');
        }

        return redirect()->route('admin.stories.index')->with('status', 'Story created.');
    }

    public function edit(AlumniStory $story): View
    {
        $this->authorize('update', $story);

        $alumniProfiles = AlumniProfile::with('user')
            ->whereNotNull('verified_at')
            ->get()
            ->sortBy(fn ($profile) => $profile->user->full_name);

        return view('admin.stories.edit', compact('story', 'alumniProfiles'));
    }

    public function update(StoreStoryRequest $request, AlumniStory $story): RedirectResponse
    {
        $this->authorize('update', $story);

        $data = $request->validated();

        if ($data['title'] !== $story->title) {
            $data['slug'] = $this->uniqueSlug($data['title'], $story->id);
        }

        if ($request->hasFile('cover_image')) {
            if ($story->cover_image) {
                Storage::disk('public')->delete($story->cover_image);
            }
            $data['cover_image'] = $request->file('cover_image')->store('stories', 'public');
        }

        $wasPublished = $story->status === AlumniStory::STATUS_PUBLISHED;

        if ($data['status'] === AlumniStory::STATUS_PUBLISHED && ! $wasPublished) {
            $data['reviewed_by'] = $request->user()->id;
            $data['published_at'] = now();
        }

        $story->update($data);

        AuditLogger::log('updated_story', $story, "Updated alumni story \"{$story->title}\".");
        Cache::forget('homepage.content');

        return redirect()->route('admin.stories.index')->with('status', 'Story updated.');
    }

    protected function uniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title);
        $slug = $base;
        $counter = 1;

        while (AlumniStory::where('slug', $slug)->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = $base . '-' . $counter++;
        }

        return $slug;
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
