@if (! $activeConversation)
    <div class="flex flex-1 items-center justify-center p-6">
        <x-empty-state icon="message-circle" title="Select a conversation" description="Marketplace buyer and seller threads will appear here." />
    </div>
@else
    <div class="flex items-center gap-3 border-b border-slate-200 bg-white p-4 dark:border-navy-800 dark:bg-navy-900">
        <button type="button" onclick="document.getElementById('thread-panel').classList.add('hidden'); document.getElementById('thread-panel').classList.remove('flex');" class="text-slate-400 hover:text-slate-600 sm:hidden">
            <x-icon name="chevron-left" class="h-5 w-5" />
        </button>
        <x-avatar :src="$otherParticipant?->avatar_url" :name="$otherParticipant?->full_name ?? 'Unknown'" />
        <div class="min-w-0 flex-1">
            <p class="truncate text-sm font-semibold text-slate-900 dark:text-white">{{ $otherParticipant?->full_name ?? 'Unknown user' }}</p>
            <p class="truncate text-xs text-slate-500 dark:text-slate-400">
                {{ $activeConversation->context === 'buyer' ? 'Buyer inquiry' : 'Seller thread' }}
                @if ($activeConversation->subject?->listing)
                    &middot; <a href="{{ route('admin.marketplace.orders.show', $activeConversation->subject) }}" class="hover:underline">{{ $activeConversation->subject->listing->title }}</a>
                @endif
            </p>
        </div>
    </div>

    <div class="flex-1 space-y-3 overflow-y-auto p-4" id="message-scroll">
        <div id="messages-container" class="space-y-3">
            @foreach ($messages as $message)
                @include('admin.messages.partials.bubble', ['message' => $message, 'authId' => auth()->id()])
            @endforeach
        </div>
    </div>

    <form onsubmit="AdminMessages.sendMessage(event, {{ $activeConversation->id }})" class="flex items-center gap-2 border-t border-slate-200 bg-white p-3 dark:border-navy-800 dark:bg-navy-900">
        <input type="text" name="body" placeholder="Write a message..." autocomplete="off" class="form-input flex-1">
        <button type="submit" class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-lg bg-navy-800 text-white hover:bg-navy-700">
            <x-icon name="send" class="h-4 w-4" />
        </button>
    </form>

    <script>document.getElementById('message-scroll') && (document.getElementById('message-scroll').scrollTop = document.getElementById('message-scroll').scrollHeight);</script>
@endif
