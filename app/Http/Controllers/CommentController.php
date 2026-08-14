<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    protected const ALLOWED_TYPES = [
        'post' => \App\Models\CommunityPost::class,
        'story' => \App\Models\AlumniStory::class,
        'news' => \App\Models\News::class,
    ];

    public function store(Request $request, string $type, int $id): RedirectResponse
    {
        abort_unless(isset(self::ALLOWED_TYPES[$type]), 404);
        abort_unless($request->user()->isVerified(), 403);

        $modelClass = self::ALLOWED_TYPES[$type];
        $commentable = $modelClass::findOrFail($id);

        $data = $request->validate([
            'body' => ['required', 'string', 'max:2000'],
            'parent_id' => ['nullable', 'exists:comments,id'],
        ]);

        $commentable->comments()->create([
            'user_id' => $request->user()->id,
            'parent_id' => $data['parent_id'] ?? null,
            'body' => $data['body'],
        ]);

        return back()->with('status', 'Comment added.');
    }

    public function destroy(Request $request, Comment $comment): RedirectResponse
    {
        abort_unless($request->user()->id === $comment->user_id || $request->user()->hasPermission('moderate-comments'), 403);

        $comment->delete();

        return back()->with('status', 'Comment removed.');
    }
}
