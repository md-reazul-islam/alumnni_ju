<?php

namespace App\Http\Controllers;

use App\Models\CarpoolBooking;
use App\Models\Comment;
use App\Models\CommunityPost;
use App\Models\MatrimonyProfile;
use App\Models\Report;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    protected const ALLOWED_TYPES = [
        'post' => CommunityPost::class,
        'comment' => Comment::class,
        'carpool_trip' => CarpoolBooking::class,
        'carpool_user' => User::class,
        'matrimony_profile' => MatrimonyProfile::class,
        'matrimony_user' => User::class,
    ];

    public function store(Request $request, string $type, int $id): RedirectResponse
    {
        abort_unless(isset(self::ALLOWED_TYPES[$type]), 404);

        $modelClass = self::ALLOWED_TYPES[$type];
        $reportable = $modelClass::findOrFail($id);

        $data = $request->validate([
            'reason' => ['required', 'string', 'max:150'],
            'details' => ['nullable', 'string', 'max:1000'],
        ]);

        Report::create([
            'reporter_id' => $request->user()->id,
            'reportable_type' => $modelClass,
            'reportable_id' => $reportable->id,
            'reason' => $data['reason'],
            'details' => $data['details'] ?? null,
        ]);

        if ($reportable instanceof CommunityPost) {
            $reportable->update(['status' => CommunityPost::STATUS_FLAGGED]);
        }

        return back()->with('status', 'Thank you. Our moderators will review this content.');
    }
}
