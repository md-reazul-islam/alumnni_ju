<x-layouts::admin :title="'Moderation'">
    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Flagged Content</h1>

    @if ($flaggedPosts->isEmpty())
        <x-empty-state icon="shield" title="Nothing flagged for review" class="mt-8" />
    @else
        <div class="mt-6 space-y-4">
            @foreach ($flaggedPosts as $post)
                <div class="card card-body">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-sm font-semibold text-slate-900 dark:text-white">{{ $post->user->full_name }}</p>
                            <p class="mt-1 line-clamp-3 text-sm text-slate-600 dark:text-slate-300">{{ $post->body }}</p>
                        </div>
                        <div class="flex flex-shrink-0 gap-2">
                            <form method="POST" action="{{ route('admin.community.posts.approve', $post) }}">
                                @csrf
                                <button type="submit" class="btn-secondary btn-sm">Restore</button>
                            </form>
                            <form method="POST" action="{{ route('admin.community.posts.remove', $post) }}">
                                @csrf
                                <button type="submit" class="btn-danger btn-sm">Remove</button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</x-layouts::admin>
