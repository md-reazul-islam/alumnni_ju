<x-layouts::admin :title="'Interest History'">
    <x-breadcrumb :items="[['label' => 'Matrimony'], ['label' => 'Reports', 'url' => route('admin.matrimony.reports.index')], ['label' => 'Interest History']]" class="mb-4" />
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Interest History</h1>
        <a href="{{ route('admin.matrimony.reports.export', 'interests') }}" class="btn-secondary btn-sm">Export CSV</a>
    </div>

    <form method="GET" class="mt-4 flex gap-3">
        <x-select label="" name="status" placeholder="All statuses" :options="['pending' => 'Pending', 'accepted' => 'Accepted', 'declined' => 'Declined', 'withdrawn' => 'Withdrawn', 'expired' => 'Expired']" :selected="request('status')" />
        <div class="flex items-end"><x-button type="submit" variant="secondary" size="sm">Filter</x-button></div>
    </form>

    @if ($interests->isEmpty())
        <x-empty-state icon="heart" title="No interest requests yet" class="mt-8" />
    @else
        <x-table class="mt-6">
            <thead><tr><th>Target Profile</th><th>Requested By</th><th>Status</th><th>Sent</th></tr></thead>
            <tbody>
                @foreach ($interests as $interest)
                    <tr>
                        <td class="font-medium text-slate-900 dark:text-white">{{ $interest->profile->display_name }} ({{ $interest->profile->creator->full_name }})</td>
                        <td>{{ $interest->requester->full_name }}</td>
                        <td><x-badge :variant="match($interest->status) { 'accepted' => 'success', 'declined', 'expired' => 'danger', 'withdrawn' => 'neutral', default => 'warning' }">{{ ucfirst($interest->status) }}</x-badge></td>
                        <td>{{ $interest->created_at->format('M j, Y') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </x-table>
        <div class="mt-6">{{ $interests->links() }}</div>
    @endif
</x-layouts::admin>
