<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Notifications\NewMessageReceived;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MessageController extends Controller
{
    public function index(Request $request, ?Conversation $conversation = null): View
    {
        abort_unless($request->user()->hasPermission('manage-marketplace'), 403);

        $user = $request->user();

        $conversations = $user->conversations()
            ->whereNotNull('context')
            ->with(['participants', 'latestMessage'])
            ->get()
            ->sortByDesc(fn ($c) => $c->latestMessage?->created_at ?? $c->created_at)
            ->values();

        [$buyerConversations, $sellerConversations] = $conversations->partition(fn ($c) => $c->context === 'buyer');

        if ($conversation) {
            abort_unless($user->conversations()->where('conversations.id', $conversation->id)->exists(), 403);
        }

        $activeConversation = $conversation ?? $conversations->first();

        $messages = collect();
        $otherParticipant = null;

        if ($activeConversation) {
            $messages = $activeConversation->messages()->with('sender')->oldest()->get();
            $otherParticipant = $activeConversation->participants->first(fn ($p) => ! $p->isAdminStaff());

            $user->conversations()->updateExistingPivot($activeConversation->id, ['last_read_at' => now()]);
        }

        if ($request->ajax() || $request->wantsJson()) {
            return view('admin.messages.partials.thread', compact('activeConversation', 'messages', 'otherParticipant'));
        }

        return view('admin.messages.index', compact('conversations', 'buyerConversations', 'sellerConversations', 'activeConversation', 'messages', 'otherParticipant'));
    }

    public function store(Request $request, Conversation $conversation): JsonResponse
    {
        $user = $request->user();
        abort_unless($user->hasPermission('manage-marketplace'), 403);
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
            'html' => view('admin.messages.partials.bubble', ['message' => $message, 'authId' => $user->id])->render(),
        ]);
    }
}
