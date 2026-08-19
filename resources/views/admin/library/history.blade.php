<x-layouts::admin :title="'Borrow History'">
    <x-breadcrumb :items="[['label' => 'Library', 'url' => route('admin.library.index')], ['label' => 'Borrow History']]" class="mb-4" />
    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Borrow History</h1>
    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">A permanent record of every completed borrow &mdash; kept for reporting even after the book is returned.</p>

    <form method="GET" class="mt-6">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by book title or borrower name" class="form-input max-w-sm">
    </form>

    @if ($borrowRequests->isEmpty())
        <x-empty-state icon="clipboard-list" title="No completed borrows yet" description="Returned books will show up here for future reference." class="mt-8" />
    @else
        <x-table class="mt-6">
            <thead><tr><th>Book</th><th>Donor</th><th>Borrower</th><th>Handed Over</th><th>Due Date</th><th>Returned</th><th></th></tr></thead>
            <tbody>
                @foreach ($borrowRequests as $request)
                    <tr>
                        <td class="font-medium text-slate-900 dark:text-white">{{ $request->book->title }}</td>
                        <td>{{ $request->book->donor->full_name }}</td>
                        <td>{{ $request->borrower->full_name }}</td>
                        <td>{{ $request->handed_over_at?->format('M j, Y') }}</td>
                        <td>{{ $request->due_date?->format('M j, Y') }}</td>
                        <td>{{ $request->returned_at?->format('M j, Y') }}</td>
                        <td>
                            @if ($request->wasReturnedLate())
                                <x-badge variant="danger">Returned late</x-badge>
                            @else
                                <x-badge variant="success">On time</x-badge>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </x-table>
        <div class="mt-6">{{ $borrowRequests->links() }}</div>
    @endif
</x-layouts::admin>
