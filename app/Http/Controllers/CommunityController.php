<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommunityPostRequest;
use App\Models\CommunityPost;
use App\Models\Poll;
use App\Services\ImageUploadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CommunityController extends Controller
{
    public function index(Request $request): View
    {
        $userId = $request->user()?->id ?? 0;

        $posts = CommunityPost::query()
            ->visible()
            ->with([
                'user',
                'poll.options.votes',
                'likes' => fn ($q) => $q->where('user_id', $userId),
            ])
            ->withCount(['comments', 'likes'])
            ->when($request->filled('category'), fn ($q) => $q->where('category', $request->string('category')))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('community.index', compact('posts'));
    }

    public function store(StoreCommunityPostRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $post = DB::transaction(function () use ($data, $request) {
            $imagePath = $request->hasFile('image') ? app(ImageUploadService::class)->store($request->file('image'), 'community', ImageUploadService::MAX_LARGE) : null;

            $post = CommunityPost::create([
                'user_id' => $request->user()->id,
                'category' => $data['category'],
                'title' => $data['title'] ?? null,
                'body' => $data['body'],
                'image' => $imagePath,
                'post_type' => $data['post_type'],
            ]);

            if ($data['post_type'] === 'poll') {
                $poll = Poll::create([
                    'community_post_id' => $post->id,
                    'question' => $data['poll_question'],
                    'expires_at' => $data['poll_expires_at'] ?? null,
                ]);

                foreach (array_filter($data['poll_options']) as $optionText) {
                    $poll->options()->create(['option_text' => $optionText]);
                }
            }

            return $post;
        });

        return redirect()->route('community.index')->with('status', 'Your post has been published.');
    }

    public function show(Request $request, CommunityPost $post): View
    {
        $userId = $request->user()?->id ?? 0;

        $post->load([
            'user',
            'poll.options.votes',
            'comments' => fn ($q) => $q->whereNull('parent_id')->with(['user', 'replies.user']),
            'likes' => fn ($q) => $q->where('user_id', $userId),
        ]);
        $post->loadCount('likes');

        return view('community.show', compact('post'));
    }

    public function destroy(Request $request, CommunityPost $post): RedirectResponse
    {
        abort_unless($request->user()->id === $post->user_id || $request->user()->hasPermission('moderate-community'), 403);

        $post->delete();

        return back()->with('status', 'Post removed.');
    }
}
