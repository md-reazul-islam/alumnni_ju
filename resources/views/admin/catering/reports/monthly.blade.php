<x-layouts::admin :title="'Monthly Report'">
    <x-breadcrumb :items="[['label' => 'Catering'], ['label' => 'Reports', 'url' => route('admin.catering.reports.index')], ['label' => 'Monthly Report']]" class="mb-4" />
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Monthly Report</h1>
        <a href="{{ route('admin.catering.reports.export', 'monthly') }}" class="btn-secondary btn-sm">Export CSV</a>
    </div>

    @if ($months->isEmpty())
        <x-empty-state icon="calendar" title="No orders yet" class="mt-8" />
    @else
        <x-table class="mt-6">
            <thead><tr><th>Month</th><th>Orders</th><th>Revenue</th></tr></thead>
            <tbody>
                @foreach ($months as $month)
                    <tr>
                        <td class="font-medium text-slate-900 dark:text-white">{{ \Illuminate\Support\Carbon::createFromFormat('Y-m', $month->month)->format('F Y') }}</td>
                        <td>{{ $month->order_count }}</td>
                        <td>${{ number_format($month->revenue, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </x-table>
        <div class="mt-6">{{ $months->links() }}</div>
    @endif
</x-layouts::admin>
