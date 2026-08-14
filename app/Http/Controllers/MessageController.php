<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\User;
use App\Notifications\NewMessageReceived;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class MessageController extends Controller
{
    public function index(Request $request, ?Conversation $conversation = null): View
    {
        $user = $request->user();

        $conversations = $user->conversations()
            ->with(['participants' => fn ($q) => $q->where('users.id', '!=', $user->id), 'latestMessage'])
            ->when($request->filled('q'), function ($q) use ($request) {
                $term = $request->string('q');
                $q->whereHas('participants', fn ($p) => $p->where('users.id', '!=', $user->id)
                    ->where(fn ($w) => $w->where('first_name', 'like', "%{$term}%")->orWhere('last_name', 'like', "%{$term}%")));
            })
            ->get()
            ->sortByDesc(fn ($c) => $c->latestMessage?->created_at ?? $c->created_at)
            ->values();

        if ($conversation) {
            abort_unless($user->conversations()->where('conversations.id', $conversation->id)->exists(), 403);
        }

        $activeConversation = $conversation ?? $conversations->first();

        $messages = collect();
        $otherParticipant = null;

        if ($activeConversation) {
            $messages = $activeConversation->messages()->with('sender')->oldest()->get();
            $otherParticipant = $activeConversation->participants()->where('users.id', '!=', $user->id)->first();

            $user->conversations()->updateExistingPivot($activeConversation->id, ['last_read_at' => now()]);
        }

        if ($request->ajax() || $request->wantsJson()) {
            return view('alumni.messages.partials.thread', compact('activeConversation', 'messages', 'otherParticipant'));
        }

        return view('alumni.messages.index', compact('conversations', 'activeConversation', 'messages', 'otherParticipant'));
    }

    public function create(Request $request, User $user): RedirectResponse
    {
        $currentUser = $request->user();
        abort_if($currentUser->id === $user->id, 422);

        $conversation = $currentUser->conversations()
            ->whereHas('participants', fn ($q) => $q->where('users.id', $user->id))
            ->first();

        if (! $conversation) {
            $conversation = DB::transaction(function () use ($currentUser, $user) {
                $conversation = Conversation::create();
                $conversation->participants()->attach([$currentUser->id, $user->id]);

                return $conversation;
            });
        }

        return redirect()->route('messages.index', $conversation);
    }

    public function store(Request $request, Conversation $conversation): JsonResponse
    {
        $user = $request->user();
        abort_unless($user->conversations()->where('conversations.id', $conversation->id)->exists(), 403);

        $data = $request->validate(['body' => ['required', 'string', 'max:5000']]);

        $message = $conversation->messages()->create([
            'user_id' => $user->id,
            'body' => $data['body'],
        ]);

        $conversation->update(['last_message_at' => now()]);
        $user->conversations()->updateExistingPivot($conversation->id, ['last_read_at' => now()]);

        $conversation->participants()->where('users.id', '!=', $user->id)->get()
            ->each(fn ($recipient) => $recipient->notify(new NewMessageReceived($message->load('sender'))));

        return response()->json([
            'message' => $message->load('sender'),
            'html' => view('alumni.messages.partials.bubble', ['message' => $message, 'authId' => $user->id])->render(),
        ]);
    }
}
