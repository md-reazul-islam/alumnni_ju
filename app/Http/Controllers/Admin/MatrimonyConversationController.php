<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MatrimonyConversationController extends Controller
{
    protected function ensurePermission(Request $request): void
    {
        abort_unless($request->user()->hasPermission('manage-matrimony'), 403);
    }

    public function index(Request $request): View
    {
        $this->ensurePermission($request);

        $conversations = Conversation::where('context', 'matrimony')
            ->with(['participants', 'latestMessage'])
            ->orderByDesc('last_message_at')
            ->paginate(20);

        return view('admin.matrimony.conversations.index', compact('conversations'));
    }

    public function show(Request $request, Conversation $conversation): View
    {
        $this->ensurePermission($request);

        abort_unless($conversation->context === 'matrimony', 404);

        $conversation->load(['participants', 'messages.sender']);

        return view('admin.matrimony.conversations.show', compact('conversation'));
    }
}
