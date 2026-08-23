<x-layouts::admin :title="'Day-wise Trips'">
    <x-breadcrumb :items="[['label' => 'Carpooling'], ['label' => 'Reports', 'url' => route('admin.carpooling.reports.index')], ['label' => 'Day-wise Trips']]" class="mb-4" />
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Day-wise Trips</h1>
        <a href="{{ route('admin.carpooling.reports.export', 'days') }}" class="btn-secondary btn-sm">Export CSV</a>
    </div>

    @if ($days->isEmpty())
        <x-empty-state icon="calendar" title="No trips scheduled yet" class="mt-8" />
    @else
        <x-table class="mt-6">
            <thead><tr><th>Date</th><th>Trips</th><th>Seats Offered</th><th>Seats Booked</th></tr></thead>
            <tbody>
                @foreach ($days as $day)
                    <tr>
                        <td class="font-medium text-slate-900 dark:text-white">{{ \Illuminate\Support\Carbon::parse($day->departure_date)->format('M j, Y') }}</td>
                        <td>{{ $day->trip_count }}</td>
                        <td>{{ $day->seats_offered }}</td>
                        <td>{{ $day->seats_booked }}</td>
                    </tr>
                @endforeach
            </tbody>
        </x-table>
        <div class="mt-6">{{ $days->links() }}</div>
    @endif
</x-layouts::admin>
