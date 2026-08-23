<x-layouts::admin :title="'Driver History'">
    <x-breadcrumb :items="[['label' => 'Carpooling'], ['label' => 'Reports', 'url' => route('admin.carpooling.reports.index')], ['label' => 'Driver History']]" class="mb-4" />
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Driver History</h1>
        <a href="{{ route('admin.carpooling.reports.export', 'drivers') }}" class="btn-secondary btn-sm">Export CSV</a>
    </div>

    @if ($drivers->isEmpty())
        <x-empty-state icon="car" title="No drivers yet" class="mt-8" />
    @else
        <x-table class="mt-6">
            <thead><tr><th>Driver</th><th>Status</th><th>Trips Posted</th><th>Completed</th><th>Cancelled</th><th>Total Earned</th></tr></thead>
            <tbody>
                @foreach ($drivers as $driver)
                    <tr>
                        <td class="font-medium text-slate-900 dark:text-white">
                            <a href="{{ route('admin.carpooling.drivers.show', $driver) }}" class="hover:underline">{{ $driver->user->full_name }}</a>
                        </td>
                        <td><x-badge :variant="match($driver->status) { 'approved' => 'success', 'rejected', 'suspended' => 'danger', default => 'warning' }">{{ ucfirst($driver->status) }}</x-badge></td>
                        <td>{{ $driver->trips_count }}</td>
                        <td>{{ $driver->completed_trips_count }}</td>
                        <td>{{ $driver->cancelled_trips_count }}</td>
                        <td class="font-semibold text-slate-900 dark:text-white">${{ number_format($driver->total_earned, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </x-table>
        <div class="mt-6">{{ $drivers->links() }}</div>
    @endif
</x-layouts::admin>
