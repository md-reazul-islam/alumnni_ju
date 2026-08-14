<x-layouts::admin :title="'Alumni Stories'">
    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Alumni Stories</h1>

    <form method="GET" class="mt-6">
        <select name="status" onchange="this.form.submit()" class="form-select max-w-xs">
            <option value="">All statuses</option>
            @foreach (['draft', 'pending_review', 'published', 'rejected'] as $status)
                <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst(str_replace('_', ' ', $status)) }}</option>
            @endforeach
        </select>
    </form>

    @if ($stories->isEmpty())
        <x-empty-state icon="book-open" title="No stories found" class="mt-8" />
    @else
        <div class="mt-6 space-y-4">
            @foreach ($stories as $story)
                <div class="card card-body">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="font-semibold text-slate-900 dark:text-white">{{ $story->title }}</p>
                            <p class="text-sm text-slate-500 dark:text-slate-400">by {{ $story->alumniProfile->user->full_name }}</p>
                            <x-badge :variant="match($story->status) { 'published' => 'success', 'pending_review' => 'warning', 'rejected' => 'danger', default => 'neutral' }" class="mt-2">
                                {{ ucfirst(str_replace('_', ' ', $story->status)) }}
                            </x-badge>
                        </div>
                        @if ($story->status === 'pending_review')
                            <div class="flex flex-shrink-0 gap-2">
                                <form method="POST" action="{{ route('admin.stories.publish', $story) }}">
                                    @csrf
                                    <button type="submit" class="btn-primary btn-sm">Publish</button>
                                </form>
                                <form method="POST" action="{{ route('admin.stories.reject', $story) }}">
                                    @csrf
                                    <button type="submit" class="btn-secondary btn-sm">Reject</button>
                                </form>
                            </div>
                        @endif
                    </div>
                    <p class="mt-3 line-clamp-3 text-sm text-slate-500 dark:text-slate-400">{{ $story->story }}</p>
                </div>
            @endforeach
        </div>
        <div class="mt-6">{{ $stories->links() }}</div>
    @endif
</x-layouts::admin>
