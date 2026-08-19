<x-layouts::alumni :title="'My Borrowed Books'">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">My Borrowed Books</h1>
        <x-button :href="route('library.index')" size="sm" variant="secondary">Browse Library</x-button>
    </div>

    @if ($borrowRequests->isEmpty())
        <x-empty-state icon="book-open" title="You haven't requested any books yet" class="mt-8" />
    @else
        <div class="mt-6 space-y-3">
            @foreach ($borrowRequests as $request)
                <div class="card card-body flex items-start justify-between gap-4">
                    <div>
                        <p class="font-semibold text-slate-900 dark:text-white">{{ $request->book->title }}</p>
                        <p class="text-sm text-slate-500 dark:text-slate-400">{{ $request->duration_months }} month(s)</p>
                        @if ($request->status === 'handed_over')
                            <p class="mt-1 text-xs {{ $request->isOverdue() ? 'font-medium text-red-600' : 'text-slate-400' }}">
                                {{ $request->isOverdue() ? 'Overdue since' : 'Due back' }} {{ $request->due_date?->format('M j, Y') }}
                            </p>
                        @endif
                    </div>
                    <div class="flex flex-shrink-0 items-center gap-2">
                        <x-badge :variant="match($request->status) {
                            'approved' => 'info',
                            'handed_over' => 'success',
                            'rejected' => 'danger',
                            'returned' => 'neutral',
                            default => 'warning',
                        }">
                            {{ ucwords(str_replace('_', ' ', $request->status)) }}
                        </x-badge>
                        @if ($request->status === 'pending')
                            <form method="POST" action="{{ route('library.requests.cancel', $request) }}" onsubmit="event.preventDefault(); Swal.fire({title:'Cancel this request?',icon:'warning',showCancelButton:true,confirmButtonColor:'#dc2626',confirmButtonText:'Cancel'}).then(r=>r.isConfirmed&&this.submit())">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-sm font-medium text-red-600 hover:underline">Cancel</button>
                            </form>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
        <div class="mt-6">{{ $borrowRequests->links() }}</div>
    @endif
</x-layouts::alumni>
