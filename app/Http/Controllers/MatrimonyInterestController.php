<?php

namespace App\Http\Controllers;

use App\Models\MatrimonyInterest;
use App\Models\MatrimonyProfile;
use App\Notifications\MatrimonyInterestAccepted;
use App\Notifications\MatrimonyInterestDeclined;
use App\Notifications\MatrimonyInterestReceived;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MatrimonyInterestController extends Controller
{
    public function mine(Request $request): View
    {
        $received = MatrimonyInterest::whereIn('matrimony_profile_id', $request->user()->matrimonyProfiles()->pluck('id'))
            ->with(['profile', 'requester', 'requesterProfile'])
            ->latest()
            ->get();

        $sent = $request->user()->matrimonyInterestsSent()
            ->with(['profile', 'requesterProfile'])
            ->latest()
            ->get();

        return view('matrimony.interests.mine', compact('received', 'sent'));
    }

    public function store(Request $request, MatrimonyProfile $profile): RedirectResponse
    {
        abort_if($profile->created_by === $request->user()->id, 422, 'You cannot send an interest request to your own profile.');
        abort_unless($profile->status === MatrimonyProfile::STATUS_APPROVED && $profile->is_active, 422, 'This profile is not open to introductions right now.');

        $data = $request->validate(['message' => ['nullable', 'string', 'max:1000']]);

        $requesterProfileId = $request->user()->matrimonyProfiles()
            ->where('status', MatrimonyProfile::STATUS_APPROVED)
            ->value('id');

        $existing = MatrimonyInterest::where('matrimony_profile_id', $profile->id)
            ->where('requested_by', $request->user()->id)
            ->first();

        if ($existing && in_array($existing->status, [MatrimonyInterest::STATUS_PENDING, MatrimonyInterest::STATUS_ACCEPTED], true)) {
            return back()->with('status', 'You already have a request with this profile.');
        }

        $attributes = [
            'requester_profile_id' => $requesterProfileId,
            'status' => MatrimonyInterest::STATUS_PENDING,
            'message' => $data['message'] ?? null,
            'responded_at' => null,
            'responded_by' => null,
        ];

        if ($existing) {
            $existing->update($attributes);
            $interest = $existing;
        } else {
            $interest = MatrimonyInterest::create($attributes + [
                'matrimony_profile_id' => $profile->id,
                'requested_by' => $request->user()->id,
            ]);
        }

        $profile->creator->notify(new MatrimonyInterestReceived($interest));

        return back()->with('status', 'Interest request sent.');
    }

    public function accept(Request $request, MatrimonyInterest $interest): RedirectResponse
    {
        $this->ensureOwnsTarget($request, $interest);
        abort_unless($interest->status === MatrimonyInterest::STATUS_PENDING, 422, 'This request has already been responded to.');

        $interest->update([
            'status' => MatrimonyInterest::STATUS_ACCEPTED,
            'responded_at' => now(),
            'responded_by' => $request->user()->id,
        ]);

        $interest->requester->notify(new MatrimonyInterestAccepted($interest));

        return back()->with('status', 'Interest accepted. You can now message each other.');
    }

    public function decline(Request $request, MatrimonyInterest $interest): RedirectResponse
    {
        $this->ensureOwnsTarget($request, $interest);
        abort_unless($interest->status === MatrimonyInterest::STATUS_PENDING, 422, 'This request has already been responded to.');

        $interest->update([
            'status' => MatrimonyInterest::STATUS_DECLINED,
            'responded_at' => now(),
            'responded_by' => $request->user()->id,
        ]);

        $interest->requester->notify(new MatrimonyInterestDeclined($interest));

        return back()->with('status', 'Interest declined.');
    }

    public function withdraw(Request $request, MatrimonyInterest $interest): RedirectResponse
    {
        abort_unless($interest->requested_by === $request->user()->id, 403);
        abort_unless($interest->status === MatrimonyInterest::STATUS_PENDING, 422, 'This request can no longer be withdrawn.');

        $interest->update([
            'status' => MatrimonyInterest::STATUS_WITHDRAWN,
            'responded_at' => now(),
            'responded_by' => $request->user()->id,
        ]);

        return back()->with('status', 'Request withdrawn.');
    }

    protected function ensureOwnsTarget(Request $request, MatrimonyInterest $interest): void
    {
        abort_unless($interest->profile->created_by === $request->user()->id, 403);
    }
}
