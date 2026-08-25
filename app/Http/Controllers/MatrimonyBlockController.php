<?php

namespace App\Http\Controllers;

use App\Models\MatrimonyBlock;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MatrimonyBlockController extends Controller
{
    public function index(Request $request): View
    {
        $blocks = $request->user()->matrimonyBlocksMade()->with('blocked')->latest()->get();

        return view('matrimony.blocks.index', compact('blocks'));
    }

    public function store(Request $request, User $user): RedirectResponse
    {
        abort_if($user->id === $request->user()->id, 422, 'You cannot block yourself.');

        $data = $request->validate(['reason' => ['nullable', 'string', 'max:255']]);

        MatrimonyBlock::firstOrCreate(
            ['blocker_id' => $request->user()->id, 'blocked_id' => $user->id],
            ['reason' => $data['reason'] ?? null]
        );

        return back()->with('status', 'User blocked. Their matrimony profiles are now hidden from you and yours from them.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        MatrimonyBlock::where('blocker_id', $request->user()->id)
            ->where('blocked_id', $user->id)
            ->delete();

        return back()->with('status', 'User unblocked.');
    }
}
