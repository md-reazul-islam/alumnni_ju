<x-layouts::admin :title="'Rejected Borrow Requests'">
    <x-breadcrumb :items="[['label' => 'Library', 'url' => route('admin.library.index')], ['label' => 'Rejected Requests']]" class="mb-4" />
    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Rejected Borrow Requests</h1>

    @if ($borrowRequests->isEmpty())
        <x-empty-state icon="circle-x" title="No rejected borrow requests" class="mt-8" />
    @else
        <x-table class="mt-6">
            <thead><tr><th>Book</th><th>Requested By</th><th>Duration</th><th>Rejected</th></tr></thead>
            <tbody>
                @foreach ($borrowRequests as $request)
                    <tr>
                        <td class="font-medium text-slate-900 dark:text-white">{{ $request->book->title }}</td>
                        <td>{{ $request->borrower->full_name }}</td>
                        <td>{{ $request->duration_months }} month(s)</td>
                        <td>{{ $request->reviewed_at?->diffForHumans() }}</td>
                    </tr>
                @endforeach
            </tbody>
        </x-table>
        <div class="mt-6">{{ $borrowRequests->links() }}</div>
    @endif
</x-layouts::admin>
