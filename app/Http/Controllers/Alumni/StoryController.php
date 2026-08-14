<?php

namespace App\Http\Controllers\Alumni;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAlumniStoryRequest;
use App\Models\AlumniStory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class StoryController extends Controller
{
    public function create(): View
    {
        $this->authorize('create', AlumniStory::class);

        return view('alumni.stories.create');
    }

    public function store(StoreAlumniStoryRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['alumni_profile_id'] = $request->user()->alumniProfile->id;
        $data['slug'] = $this->uniqueSlug($data['title']);
        $data['status'] = AlumniStory::STATUS_PENDING_REVIEW;

        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $request->file('cover_image')->store('stories', 'public');
        }

        AlumniStory::create($data);

        return redirect()->route('stories.mine')->with('status', 'Your story has been submitted for review.');
    }

    public function mine(Request $request): View
    {
        $stories = $request->user()->alumniProfile->stories()->latest()->paginate(10);

        return view('alumni.stories.mine', compact('stories'));
    }

    protected function uniqueSlug(string $title): string
    {
        $base = Str::slug($title);
        $slug = $base;
        $counter = 1;

        while (AlumniStory::where('slug', $slug)->exists()) {
            $slug = $base . '-' . $counter++;
        }

        return $slug;
    }
}
