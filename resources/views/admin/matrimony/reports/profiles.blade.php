<x-layouts::admin :title="'All Matrimony Profiles'">
    <x-breadcrumb :items="[['label' => 'Matrimony'], ['label' => 'Reports', 'url' => route('admin.matrimony.reports.index')], ['label' => 'All Profiles']]" class="mb-4" />
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">All Matrimony Profiles</h1>
        <a href="{{ route('admin.matrimony.reports.export', 'profiles') }}" class="btn-secondary btn-sm">Export CSV</a>
    </div>

    <form method="GET" class="mt-4 flex flex-wrap gap-3">
        <x-select label="" name="status" placeholder="All statuses" :options="['draft' => 'Draft', 'pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected', 'suspended' => 'Suspended']" :selected="request('status')" />
        <x-input label="" name="country" placeholder="Filter by country" :value="request('country')" />
        <div class="flex items-end"><x-button type="submit" variant="secondary" size="sm">Filter</x-button></div>
    </form>

    @if ($profiles->isEmpty())
        <x-empty-state icon="heart" title="No profiles" class="mt-8" />
    @else
        <x-table class="mt-6">
            <thead><tr><th>Profile</th><th>Managed By</th><th>Status</th><th>Country</th><th>Interests Received</th><th>Views</th></tr></thead>
            <tbody>
                @foreach ($profiles as $profile)
                    <tr>
                        <td class="font-medium text-slate-900 dark:text-white">
                            <a href="{{ route('admin.matrimony.profiles.show', $profile) }}" class="hover:underline">{{ $profile->display_name }}</a>
                        </td>
                        <td>{{ $profile->creator->full_name }}</td>
                        <td><x-badge :variant="match($profile->status) { 'approved' => 'success', 'rejected', 'suspended' => 'danger', default => 'warning' }">{{ ucfirst($profile->status) }}</x-badge></td>
                        <td>{{ $profile->country }}</td>
                        <td>{{ $profile->interests_received_count }}</td>
                        <td>{{ $profile->views_count }}</td>
                    </tr>
                @endforeach
            </tbody>
        </x-table>
        <div class="mt-6">{{ $profiles->links() }}</div>
    @endif
</x-layouts::admin>
