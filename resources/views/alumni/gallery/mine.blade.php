<x-layouts::alumni :title="'My Gallery Photos'">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">My Gallery Photos</h1>
        <x-button :href="route('gallery.create')" size="sm"><x-icon name="plus" class="h-4 w-4" /> Add a Photo</x-button>
    </div>

    @if ($photos->isEmpty())
        <x-empty-state icon="image" title="You haven't added any photos yet" class="mt-8" />
    @else
        <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($photos as $photo)
                <div class="card overflow-hidden">
                    <div class="aspect-video bg-slate-100 dark:bg-navy-800">
                        <img src="{{ $photo->image_url }}" class="h-full w-full object-cover" alt="{{ $photo->description ?: 'Gallery photo' }}">
                    </div>
                    <div class="card-body">
                        <x-badge :variant="match($photo->status) { 'approved' => 'success', 'rejected' => 'danger', default => 'warning' }">
                            {{ ucfirst($photo->status) }}
                        </x-badge>
                        @if ($photo->description)
                            <p class="mt-2 line-clamp-2 text-sm text-slate-600 dark:text-slate-300">{{ $photo->description }}</p>
                        @endif

                        <div class="mt-3 flex items-center gap-3">
                            <a href="{{ route('gallery.edit', $photo) }}" class="text-sm font-medium text-navy-700 hover:underline dark:text-navy-300">Edit</a>
                            <form method="POST" action="{{ route('gallery.destroy', $photo) }}" onsubmit="event.preventDefault(); confirmDelete(this);">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-sm font-medium text-red-600 hover:underline">Delete</button>
                            </form>
                        </div>
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
</x-layouts::alumni>
