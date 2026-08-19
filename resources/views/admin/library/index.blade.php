<x-layouts::admin :title="'Library'">
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Library &mdash; Books</h1>
        <div class="flex flex-wrap gap-2">
            <x-button :href="route('admin.library.requests.pending')" variant="secondary" size="sm">Pending Requests</x-button>
            <x-button :href="route('admin.library.requests.accepted')" variant="secondary" size="sm">Accepted Requests</x-button>
            <x-button :href="route('admin.library.requests.rejected')" variant="secondary" size="sm">Rejected Requests</x-button>
            <x-button :href="route('admin.library.requests.borrowed')" variant="secondary" size="sm">Borrowed Books</x-button>
            <x-button :href="route('admin.library.create')" size="sm">+ Add Book</x-button>
        </div>
    </div>

    <form method="GET" class="mt-6">
        <select name="status" onchange="this.form.submit()" class="form-select max-w-xs">
            <option value="">All statuses</option>
            @foreach (['pending', 'approved', 'rejected'] as $status)
                <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
            @endforeach
        </select>
    </form>

    @if ($books->isEmpty())
        <x-empty-state icon="book-open" title="No books found" class="mt-8" />
    @else
        <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($books as $book)
                <div class="card overflow-hidden">
                    <div class="flex aspect-video items-center justify-center bg-gold-50 text-gold-500 dark:bg-navy-800">
                        @if ($book->cover_url)
                            <img src="{{ $book->cover_url }}" class="h-full w-full object-cover" alt="{{ $book->title }}">
                        @else
                            <x-icon name="book-open" class="h-9 w-9" />
                        @endif
                    </div>
                    <div class="card-body">
                        <div class="flex items-start justify-between gap-2">
                            <div class="min-w-0">
                                <p class="truncate font-semibold text-slate-900 dark:text-white">{{ $book->title }}</p>
                                <x-badge :variant="match($book->status) { 'approved' => 'success', 'rejected' => 'danger', default => 'warning' }" class="mt-1">
                                    {{ ucfirst($book->status) }}
                                </x-badge>
                            </div>
                            <div class="flex flex-shrink-0 items-center gap-2">
                                <a href="{{ route('admin.library.edit', $book) }}" class="text-slate-400 hover:text-navy-700" title="Edit"><x-icon name="pencil" class="h-4 w-4" /></a>
                                <form method="POST" action="{{ route('admin.library.destroy', $book) }}" onsubmit="event.preventDefault(); Swal.fire({title:'Delete this book?',icon:'warning',showCancelButton:true,confirmButtonColor:'#dc2626',confirmButtonText:'Delete'}).then(r=>r.isConfirmed&&this.submit())">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-slate-400 hover:text-red-600" title="Delete"><x-icon name="trash-2" class="h-4 w-4" /></button>
                                </form>
                            </div>
                        </div>

                        <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">Donated by {{ $book->donor->full_name }}</p>

                        @if ($book->status === 'pending')
                            <div class="mt-3 flex gap-2">
                                <form method="POST" action="{{ route('admin.library.approve', $book) }}">
                                    @csrf
                                    <button type="submit" class="btn-primary btn-sm">Accept</button>
                                </form>
                                <form method="POST" action="{{ route('admin.library.reject', $book) }}">
                                    @csrf
                                    <button type="submit" class="btn-secondary btn-sm">Decline</button>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
        <div class="mt-6">{{ $books->links() }}</div>
    @endif
</x-layouts::admin>
