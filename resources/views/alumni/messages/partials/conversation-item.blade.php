@php $other = $conversation->participants->first(); @endphp
<button
    type="button"
    onclick="AlumniMessages.openConversation({{ $conversation->id }}, this)"
    data-conversation-id="{{ $conversation->id }}"
    class="flex w-full items-center gap-3 px-4 py-3 text-left hover:bg-slate-50 dark:hover:bg-navy-800 {{ $activeConversation?->id === $conversation->id ? 'bg-navy-50 dark:bg-navy-800' : '' }}"
>
    <x-avatar :src="$other?->avatar_url" :name="$other?->full_name ?? 'Unknown'" />
    <div class="min-w-0 flex-1">
        <p class="truncate text-sm font-semibold text-slate-900 dark:text-white">{{ $other?->full_name ?? 'Unknown user' }}</p>
        <p class="truncate text-xs text-slate-500 dark:text-slate-400">{{ Str::limit($conversation->latestMessage?->body, 40) ?: 'No messages yet' }}</p>
    </div>
</button>
