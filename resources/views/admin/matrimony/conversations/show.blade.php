<x-layouts::admin :title="'Conversation'">
    <x-breadcrumb :items="[['label' => 'Matrimony'], ['label' => 'Conversations', 'url' => route('admin.matrimony.conversations.index')], ['label' => 'Conversation']]" class="mb-4" />
    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $conversation->participants->pluck('full_name')->join(' & ') }}</h1>
    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Started {{ $conversation->created_at->format('M j, Y') }}</p>

    <div class="card card-body mt-6 space-y-4">
        @forelse ($conversation->messages as $message)
            <div class="flex gap-3">
                <div class="flex-1">
                    <p class="text-sm font-semibold text-slate-900 dark:text-white">{{ $message->sender->full_name }} <span class="ml-1 text-xs font-normal text-slate-400">{{ $message->created_at->format('M j, g:i A') }}</span></p>
                    <p class="mt-1 whitespace-pre-line text-sm text-slate-600 dark:text-slate-300">{{ $message->body }}</p>
                </div>
            </div>
        @empty
            <x-empty-state icon="message-circle" title="No messages yet" class="py-6" />
        @endforelse
    </div>
</x-layouts::admin>
