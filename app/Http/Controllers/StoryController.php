<?php

namespace App\Http\Controllers;

use App\Models\AlumniStory;
use Illuminate\View\View;

class StoryController extends Controller
{
    public function index(): View
    {
        $stories = AlumniStory::published()
            ->with('alumniProfile.user')
            ->latest('published_at')
            ->paginate(9);

        return view('public.stories.index', compact('stories'));
    }

    public function show(AlumniStory $story): View
    {
        abort_unless($story->status === AlumniStory::STATUS_PUBLISHED, 404);

        $story->load('alumniProfile.user');
        $story->increment('views');

        return view('public.stories.show', compact('story'));
    }
}
