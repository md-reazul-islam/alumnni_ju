<x-layouts::admin :title="'Daily Report'">
    <x-breadcrumb :items="[['label' => 'Catering'], ['label' => 'Reports', 'url' => route('admin.catering.reports.index')], ['label' => 'Daily Report']]" class="mb-4" />
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Daily Report</h1>
        <a href="{{ route('admin.catering.reports.export', 'daily') }}" class="btn-secondary btn-sm">Export CSV</a>
    </div>

    @if ($days->isEmpty())
        <x-empty-state icon="calendar" title="No orders yet" class="mt-8" />
    @else
        <x-table class="mt-6">
            <thead><tr><th>Event Date</th><th>Orders</th><th>Revenue</th></tr></thead>
            <tbody>
                @foreach ($days as $day)
                    <tr>
                        <td class="font-medium text-slate-900 dark:text-white">{{ \Illuminate\Support\Carbon::parse($day->event_date)->format('M j, Y') }}</td>
                        <td>{{ $day->order_count }}</td>
                        <td>${{ number_format($day->revenue, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </x-table>
        <div class="mt-6">{{ $days->links() }}</div>
    @endif
</x-layouts::admin>
