<x-layouts::admin :title="'News'">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">News &amp; Announcements</h1>
        <x-button :href="route('admin.news.create')" size="sm"><x-icon name="plus" class="h-4 w-4" /> Create Article</x-button>
    </div>

    <form method="GET" class="mt-6 flex flex-col gap-3 sm:flex-row">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search articles" class="form-input sm:max-w-xs">
        <select name="status" class="form-select sm:max-w-xs">
            <option value="">All statuses</option>
            @foreach (['draft', 'pending', 'published', 'scheduled', 'archived'] as $status)
                <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
            @endforeach
        </select>
        <x-button type="submit" variant="secondary">Filter</x-button>
    </form>

    @if ($news->isEmpty())
        <x-empty-state icon="newspaper" title="No articles yet" class="mt-8" />
    @else
        <x-table class="mt-6">
            <thead><tr><th>Title</th><th>Category</th><th>Author</th><th>Status</th><th></th></tr></thead>
            <tbody>
                @foreach ($news as $article)
                    <tr>
                        <td class="font-medium text-slate-900 dark:text-white">{{ $article->title }}</td>
                        <td>{{ $article->category?->name }}</td>
                        <td>{{ $article->author->full_name }}</td>
                        <td><x-badge :variant="$article->status === 'published' ? 'success' : 'neutral'">{{ ucfirst($article->status) }}</x-badge></td>
                        <td>
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.news.edit', $article) }}" class="text-slate-400 hover:text-navy-700"><x-icon name="pencil" class="h-4 w-4" /></a>
                                <form method="POST" action="{{ route('admin.news.destroy', $article) }}" onsubmit="event.preventDefault(); Swal.fire({title:'Delete this article?',icon:'warning',showCancelButton:true,confirmButtonColor:'#dc2626',confirmButtonText:'Delete'}).then(r=>r.isConfirmed&&this.submit())">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-slate-400 hover:text-red-600"><x-icon name="trash-2" class="h-4 w-4" /></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </x-table>
        <div class="mt-6">{{ $news->links() }}</div>
    @endif
</x-layouts::admin>
