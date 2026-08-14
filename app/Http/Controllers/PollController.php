<?php

namespace App\Http\Controllers;

use App\Models\Poll;
use App\Models\PollVote;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PollController extends Controller
{
    public function vote(Request $request, Poll $poll): RedirectResponse
    {
        abort_if($poll->hasExpired(), 422, 'This poll has closed.');

        $data = $request->validate(['option_id' => ['required', 'exists:poll_options,id']]);

        $option = $poll->options()->findOrFail($data['option_id']);

        PollVote::updateOrCreate(
            ['poll_id' => $poll->id, 'user_id' => $request->user()->id],
            ['poll_option_id' => $option->id]
        );

        return back()->with('status', 'Vote recorded.');
    }
}
