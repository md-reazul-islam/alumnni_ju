<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CommunityPost;
use App\Models\Report;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CommunityController extends Controller
{
    protected function ensureModerator(Request $request): void
    {
        abort_unless($request->user()->hasPermission('moderate-community'), 403);
    }

    public function posts(Request $request): View
    {
        $this->ensureModerator($request);

        $posts = CommunityPost::query()
            ->with('user')
            ->withCount(['comments', 'likes'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.community.posts', compact('posts'));
    }

    public function reports(Request $request): View
    {
        $this->ensureModerator($request);

        $reports = Report::with(['reporter', 'reportable'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')), fn ($q) => $q->pending())
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.community.reports', compact('reports'));
    }

    public function moderation(Request $request): View
    {
        $this->ensureModerator($request);

        $flaggedPosts = CommunityPost::where('status', CommunityPost::STATUS_FLAGGED)->with('user')->latest()->get();

        return view('admin.community.moderation', compact('flaggedPosts'));
    }

    public function approvePost(Request $request, CommunityPost $post): RedirectResponse
    {
        $this->ensureModerator($request);

        $post->update(['status' => CommunityPost::STATUS_PUBLISHED]);
        Report::where('reportable_type', CommunityPost::class)->where('reportable_id', $post->id)->update(['status' => Report::STATUS_DISMISSED, 'reviewed_by' => $request->user()->id, 'reviewed_at' => now()]);

        return back()->with('status', 'Post restored.');
    }

    public function removePost(Request $request, CommunityPost $post): RedirectResponse
    {
        $this->ensureModerator($request);

        $post->update(['status' => CommunityPost::STATUS_REMOVED]);
        Report::where('reportable_type', CommunityPost::class)->where('reportable_id', $post->id)->update(['status' => Report::STATUS_ACTION_TAKEN, 'reviewed_by' => $request->user()->id, 'reviewed_at' => now()]);

        return back()->with('status', 'Post removed.');
    }

    public function dismissReport(Request $request, Report $report): RedirectResponse
    {
        $this->ensureModerator($request);

        $report->update(['status' => Report::STATUS_DISMISSED, 'reviewed_by' => $request->user()->id, 'reviewed_at' => now()]);

        return back()->with('status', 'Report dismissed.');
    }
}
