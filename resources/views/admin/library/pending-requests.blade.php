<x-layouts::admin :title="'Pending Borrow Requests'">
    <x-breadcrumb :items="[['label' => 'Library', 'url' => route('admin.library.index')], ['label' => 'Pending Requests']]" class="mb-4" />
    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Pending Borrow Requests</h1>

    @if ($borrowRequests->isEmpty())
        <x-empty-state icon="clipboard-check" title="No borrow requests awaiting review" class="mt-8" />
    @else
        <div class="mt-6 space-y-4">
            @foreach ($borrowRequests as $request)
                <div class="card card-body flex items-start justify-between gap-4">
                    <div>
                        <p class="font-semibold text-slate-900 dark:text-white">{{ $request->book->title }}</p>
                        <p class="text-sm text-slate-500 dark:text-slate-400">Requested by {{ $request->borrower->full_name }} &middot; {{ $request->duration_months }} month(s)</p>
                        <p class="mt-1 text-xs text-slate-400">Requested {{ $request->created_at->diffForHumans() }}</p>
                    </div>
                    <div class="flex flex-shrink-0 gap-2">
                        <form method="POST" action="{{ route('admin.library.requests.approve', $request) }}">
                            @csrf
                            <button type="submit" class="btn-primary btn-sm">Accept</button>
                        </form>
                        <form method="POST" action="{{ route('admin.library.requests.reject', $request) }}">
                            @csrf
                            <button type="submit" class="btn-secondary btn-sm">Reject</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="mt-6">{{ $borrowRequests->links() }}</div>
    @endif
</x-layouts::admin>
