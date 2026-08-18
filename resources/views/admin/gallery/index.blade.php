<x-layouts::admin :title="'Gallery'">
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Gallery</h1>
        <x-button :href="route('admin.gallery.create')" size="sm">
            <x-icon name="plus" class="h-4 w-4" /> Add Photo
        </x-button>
    </div>

    <form method="GET" class="mt-6">
        <select name="status" onchange="this.form.submit()" class="form-select max-w-xs">
            <option value="">All statuses</option>
            @foreach (['pending', 'approved', 'rejected'] as $status)
                <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
            @endforeach
        </select>
    </form>

    @if ($photos->isEmpty())
        <x-empty-state icon="image" title="No gallery photos found" class="mt-8" />
    @else
        <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($photos as $photo)
                <div class="card overflow-hidden">
                    <div class="aspect-video bg-slate-100 dark:bg-navy-800">
                        <img src="{{ $photo->image_url }}" class="h-full w-full object-cover" alt="{{ $photo->description ?: 'Gallery photo' }}">
                    </div>
                    <div class="card-body">
                        <div class="flex items-start justify-between gap-2">
                            <div class="min-w-0">
                                <p class="truncate text-sm font-semibold text-slate-900 dark:text-white">{{ $photo->user->full_name }}</p>
                                <x-badge :variant="match($photo->status) { 'approved' => 'success', 'rejected' => 'danger', default => 'warning' }" class="mt-1">
                                    {{ ucfirst($photo->status) }}
                                </x-badge>
                            </div>
                            <div class="flex flex-shrink-0 items-center gap-2">
                                <a href="{{ route('admin.gallery.edit', $photo) }}" class="text-slate-400 hover:text-navy-700" title="Edit"><x-icon name="pencil" class="h-4 w-4" /></a>
                                <form method="POST" action="{{ route('admin.gallery.destroy', $photo) }}" onsubmit="event.preventDefault(); confirmDelete(this);">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-slate-400 hover:text-red-600" title="Delete"><x-icon name="trash-2" class="h-4 w-4" /></button>
                                </form>
                            </div>
                        </div>

                        @if ($photo->description)
                            <p class="mt-2 line-clamp-2 text-sm text-slate-500 dark:text-slate-400">{{ $photo->description }}</p>
                        @endif

                        @if ($photo->status === 'pending')
                            <div class="mt-3 flex gap-2">
                                <form method="POST" action="{{ route('admin.gallery.approve', $photo) }}">
                                    @csrf
                                    <button type="submit" class="btn-primary btn-sm">Accept</button>
                                </form>
                                <form method="POST" action="{{ route('admin.gallery.reject', $photo) }}">
                                    @csrf
                                    <button type="submit" class="btn-secondary btn-sm">Decline</button>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
        <div class="mt-6">{{ $photos->links() }}</div>
    @endif

    @push('scripts')
    <script>
        function confirmDelete(form) {
            Swal.fire({
                title: 'Delete this photo?',
                text: 'This action cannot be undone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Delete',
                confirmButtonColor: '#dc2626',
            }).then((result) => { if (result.isConfirmed) form.submit(); });
        }
    </script>
    @endpush
</x-layouts::admin>
