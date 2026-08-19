<x-layouts::alumni :title="'My Donations'">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">My Donations</h1>
        <x-button :href="route('library.create')" size="sm"><x-icon name="plus" class="h-4 w-4" /> Donate a Book</x-button>
    </div>

    @if ($books->isEmpty())
        <x-empty-state icon="book-open" title="You haven't donated any books yet" class="mt-8" />
    @else
        <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($books as $book)
                @php $activeBorrow = $book->borrowRequests->first(); @endphp
                <div class="card overflow-hidden">
                    <div class="flex aspect-video items-center justify-center bg-gold-50 text-gold-500 dark:bg-navy-800">
                        @if ($book->cover_url)
                            <img src="{{ $book->cover_url }}" class="h-full w-full object-cover" alt="{{ $book->title }}">
                        @else
                            <x-icon name="book-open" class="h-9 w-9" />
                        @endif
                    </div>
                    <div class="card-body">
                        <x-badge :variant="match($book->status) { 'approved' => 'success', 'rejected' => 'danger', default => 'warning' }">
                            {{ ucfirst($book->status) }}
                        </x-badge>
                        <p class="mt-2 font-semibold text-slate-900 dark:text-white">{{ $book->title }}</p>
                        @if ($book->author)
                            <p class="text-xs text-slate-500 dark:text-slate-400">{{ $book->author }}</p>
                        @endif

                        @if ($activeBorrow)
                            <div class="mt-3 rounded-lg bg-navy-50 p-3 text-xs text-navy-700 dark:bg-navy-800 dark:text-navy-200">
                                <p class="font-medium">Currently with {{ $activeBorrow->borrower->full_name }}</p>
                                <p class="mt-0.5">
                                    {{ $activeBorrow->status === 'handed_over' ? 'Due back ' . $activeBorrow->due_date?->format('M j, Y') : 'Awaiting collection' }}
                                </p>
                            </div>
                        @endif

                        <div class="mt-3 flex items-center gap-3">
                            @if ($book->status !== 'approved' || ! $activeBorrow)
                                <a href="{{ route('library.edit', $book) }}" class="text-sm font-medium text-navy-700 hover:underline dark:text-navy-300">Edit</a>
                                <form method="POST" action="{{ route('library.destroy', $book) }}" onsubmit="event.preventDefault(); confirmDelete(this);">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-sm font-medium text-red-600 hover:underline">Delete</button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="mt-6">{{ $books->links() }}</div>
    @endif

    @push('scripts')
    <script>
        function confirmDelete(form) {
            Swal.fire({
                title: 'Delete this book?',
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
