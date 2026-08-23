<x-layouts::admin :title="'Passenger History'">
    <x-breadcrumb :items="[['label' => 'Carpooling'], ['label' => 'Reports', 'url' => route('admin.carpooling.reports.index')], ['label' => 'Passenger History']]" class="mb-4" />
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Passenger History</h1>
        <a href="{{ route('admin.carpooling.reports.export', 'passengers') }}" class="btn-secondary btn-sm">Export CSV</a>
    </div>

    @if ($passengers->isEmpty())
        <x-empty-state icon="users" title="No passenger activity yet" class="mt-8" />
    @else
        <x-table class="mt-6">
            <thead><tr><th>Passenger</th><th>Bookings</th><th>Confirmed/Completed</th><th>Total Spent</th></tr></thead>
            <tbody>
                @foreach ($passengers as $passenger)
                    <tr>
                        <td class="font-medium text-slate-900 dark:text-white">{{ $passenger->full_name }}</td>
                        <td>{{ $passenger->bookings_count }}</td>
                        <td>{{ $passenger->confirmed_bookings_count }}</td>
                        <td class="font-semibold text-slate-900 dark:text-white">${{ number_format($passenger->total_spent ?? 0, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </x-table>
        <div class="mt-6">{{ $passengers->links() }}</div>
    @endif
</x-layouts::admin>
