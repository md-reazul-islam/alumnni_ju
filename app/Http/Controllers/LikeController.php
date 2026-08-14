<?php

namespace App\Http\Controllers;

use App\Models\AlumniStory;
use App\Models\CommunityPost;
use App\Models\Like;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    protected const ALLOWED_TYPES = [
        'post' => CommunityPost::class,
        'story' => AlumniStory::class,
    ];

    public function toggle(Request $request, string $type, int $id): JsonResponse
    {
        abort_unless(isset(self::ALLOWED_TYPES[$type]), 404);

        $modelClass = self::ALLOWED_TYPES[$type];
        $likeable = $modelClass::findOrFail($id);
        $user = $request->user();

        $existing = Like::where('user_id', $user->id)
            ->where('likeable_type', $modelClass)
            ->where('likeable_id', $likeable->id)
            ->first();

        if ($existing) {
            $existing->delete();
            $liked = false;
        } else {
            Like::create(['user_id' => $user->id, 'likeable_type' => $modelClass, 'likeable_id' => $likeable->id]);
            $liked = true;
        }

        return response()->json([
            'liked' => $liked,
            'count' => $likeable->likes()->count(),
        ]);
    }
}
