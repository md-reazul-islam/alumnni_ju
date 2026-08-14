<x-layouts::admin :title="'Community Posts'">
    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Community Posts</h1>

    <form method="GET" class="mt-6">
        <select name="status" onchange="this.form.submit()" class="form-select max-w-xs">
            <option value="">All statuses</option>
            @foreach (['published', 'flagged', 'removed'] as $status)
                <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
            @endforeach
        </select>
    </form>

    @if ($posts->isEmpty())
        <x-empty-state icon="message-square" title="No posts found" class="mt-8" />
    @else
        <x-table class="mt-6">
            <thead><tr><th>Author</th><th>Category</th><th>Comments</th><th>Likes</th><th>Status</th></tr></thead>
            <tbody>
                @foreach ($posts as $post)
                    <tr>
                        <td class="font-medium text-slate-900 dark:text-white">{{ $post->user->full_name }}</td>
                        <td>{{ ucfirst($post->category) }}</td>
                        <td>{{ $post->comments_count }}</td>
                        <td>{{ $post->likes_count }}</td>
                        <td><x-badge :variant="match($post->status) { 'published' => 'success', 'flagged' => 'warning', 'removed' => 'danger', default => 'neutral' }">{{ ucfirst($post->status) }}</x-badge></td>
                    </tr>
                @endforeach
            </tbody>
        </x-table>
        <div class="mt-6">{{ $posts->links() }}</div>
    @endif
</x-layouts::admin>
