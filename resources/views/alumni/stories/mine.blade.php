<x-layouts::alumni :title="'My Stories'">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">My Stories</h1>
        <x-button :href="route('stories.create')" size="sm"><x-icon name="plus" class="h-4 w-4" /> Share a Story</x-button>
    </div>

    @if ($stories->isEmpty())
        <x-empty-state icon="book-open" title="You haven't shared any stories yet" class="mt-8" />
    @else
        <div class="mt-6 space-y-3">
            @foreach ($stories as $story)
                <div class="card card-body flex items-center justify-between gap-4">
                    <div class="min-w-0">
                        <p class="truncate font-semibold text-slate-900 dark:text-white">{{ $story->title }}</p>
                        @if ($story->status === 'rejected' && $story->rejection_reason)
                            <p class="mt-1 text-xs text-red-500">{{ $story->rejection_reason }}</p>
                        @endif
                    </div>
                    <x-badge :variant="match($story->status) { 'published' => 'success', 'pending_review' => 'warning', 'rejected' => 'danger', default => 'neutral' }">
                        {{ ucfirst(str_replace('_', ' ', $story->status)) }}
                    </x-badge>
                </div>
            @endforeach
        </div>
        <div class="mt-6">{{ $stories->links() }}</div>
    @endif
</x-layouts::alumni>
