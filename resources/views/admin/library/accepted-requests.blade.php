<x-layouts::admin :title="'Accepted Borrow Requests'">
    <x-breadcrumb :items="[['label' => 'Library', 'url' => route('admin.library.index')], ['label' => 'Accepted Requests']]" class="mb-4" />
    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Accepted Borrow Requests</h1>
    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Approved requests waiting for the book to be handed over.</p>

    @if ($borrowRequests->isEmpty())
        <x-empty-state icon="clipboard-check" title="No books awaiting handover" class="mt-8" />
    @else
        <div class="mt-6 space-y-4">
            @foreach ($borrowRequests as $request)
                <div class="card card-body flex items-start justify-between gap-4">
                    <div>
                        <p class="font-semibold text-slate-900 dark:text-white">{{ $request->book->title }}</p>
                        <p class="text-sm text-slate-500 dark:text-slate-400">{{ $request->borrower->full_name }} &middot; {{ $request->duration_months }} month(s)</p>
                        <p class="mt-1 text-xs text-slate-400">Approved {{ $request->reviewed_at?->diffForHumans() }}</p>
                    </div>
                    <form method="POST" action="{{ route('admin.library.requests.handover', $request) }}" class="flex-shrink-0">
                        @csrf
                        <button type="submit" class="btn-primary btn-sm">Mark Handed Over</button>
                    </form>
                </div>
            @endforeach
        </div>
        <div class="mt-6">{{ $borrowRequests->links() }}</div>
    @endif
</x-layouts::admin>
