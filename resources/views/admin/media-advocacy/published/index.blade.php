<x-layouts::admin :title="'Published Media'">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Published Media</h1>
        <x-button :href="route('admin.media-advocacy.published.create')" size="sm"><x-icon name="plus" class="h-4 w-4" /> Add Item</x-button>
    </div>
    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Showcase of already-published work — banners, posters, promotional videos, news, blogs.</p>

    @if (session('status'))
        <x-alert variant="success" class="mt-4">{{ session('status') }}</x-alert>
    @endif

    <form method="GET" class="mt-6">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search media" class="form-input max-w-xs">
    </form>

    @if ($media->isEmpty())
        <x-empty-state icon="image" title="No published media yet" class="mt-8" />
    @else
        <x-table class="mt-6">
            <thead><tr><th>Preview</th><th>Title</th><th>Type</th><th>Tag</th><th>Status</th><th></th></tr></thead>
            <tbody>
                @foreach ($media as $item)
                    <tr>
                        <td>
                            <div class="flex h-12 w-16 items-center justify-center overflow-hidden rounded-lg bg-slate-100 dark:bg-navy-800">
                                @if ($item->type === 'image' && $item->image_url)
                                    <img src="{{ $item->image_url }}" class="h-full w-full object-cover">
                                @else
                                    <x-icon name="video" class="h-5 w-5 text-slate-400" />
                                @endif
                            </div>
                        </td>
                        <td class="font-medium text-slate-900 dark:text-white">{{ $item->title }}</td>
                        <td><x-badge variant="neutral">{{ ucfirst($item->type) }}</x-badge></td>
                        <td class="text-sm text-slate-500 dark:text-slate-400">{{ $item->tag ?: '—' }}</td>
                        <td><x-badge :variant="$item->is_active ? 'success' : 'neutral'">{{ $item->is_active ? 'Active' : 'Inactive' }}</x-badge></td>
                        <td class="flex items-center gap-3">
                            <a href="{{ route('admin.media-advocacy.published.edit', $item) }}" class="text-slate-400 hover:text-navy-700"><x-icon name="pencil" class="h-4 w-4" /></a>
                            <form method="POST" action="{{ route('admin.media-advocacy.published.destroy', $item) }}" onsubmit="event.preventDefault(); Swal.fire({title:'Remove this item?',icon:'warning',showCancelButton:true,confirmButtonColor:'#dc2626',confirmButtonText:'Remove'}).then(r=>r.isConfirmed&&this.submit())">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-slate-400 hover:text-red-600"><x-icon name="trash-2" class="h-4 w-4" /></button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </x-table>
        <div class="mt-6">{{ $media->links() }}</div>
    @endif
</x-layouts::admin>
