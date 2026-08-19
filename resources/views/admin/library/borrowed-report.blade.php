<x-layouts::admin :title="'Borrowed Books'">
    <x-breadcrumb :items="[['label' => 'Library', 'url' => route('admin.library.index')], ['label' => 'Borrowed Books']]" class="mb-4" />
    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Borrowed Books</h1>
    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Books currently checked out, with their due dates. Overdue borrows are highlighted.</p>

    @if ($borrowRequests->isEmpty())
        <x-empty-state icon="book-open" title="No books are currently borrowed" class="mt-8" />
    @else
        <x-table class="mt-6">
            <thead><tr><th>Book</th><th>Borrower</th><th>Handed Over</th><th>Due Date</th><th>Status</th><th></th></tr></thead>
            <tbody>
                @foreach ($borrowRequests as $request)
                    <tr class="{{ $request->isOverdue() ? 'bg-red-50 dark:bg-red-900/10' : '' }}">
                        <td class="font-medium text-slate-900 dark:text-white">{{ $request->book->title }}</td>
                        <td>{{ $request->borrower->full_name }}</td>
                        <td>{{ $request->handed_over_at?->format('M j, Y') }}</td>
                        <td>{{ $request->due_date?->format('M j, Y') }}</td>
                        <td>
                            @if ($request->isOverdue())
                                <x-badge variant="danger">Overdue</x-badge>
                            @else
                                <x-badge variant="success">On time</x-badge>
                            @endif
                        </td>
                        <td>
                            <div class="flex items-center gap-2">
                                <form method="POST" action="{{ route('admin.library.requests.remind', $request) }}">
                                    @csrf
                                    <button type="submit" class="btn-secondary btn-sm">Send Reminder</button>
                                </form>
                                <form method="POST" action="{{ route('admin.library.requests.returned', $request) }}">
                                    @csrf
                                    <button type="submit" class="btn-primary btn-sm">Mark Returned</button>
                                </form>
                            </div>
                            @if ($request->last_reminder_sent_at)
                                <p class="mt-1 text-xs text-slate-400">Last reminder {{ $request->last_reminder_sent_at->diffForHumans() }}</p>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </x-table>
        <div class="mt-6">{{ $borrowRequests->links() }}</div>
    @endif
</x-layouts::admin>
