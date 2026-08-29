<x-layouts::admin :title="'Income by Customer'">
    <x-breadcrumb :items="[['label' => 'Media Advocacy'], ['label' => 'Reports', 'url' => route('admin.media-advocacy.reports.index')], ['label' => 'Income by Customer']]" class="mb-4" />
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Income by Customer</h1>
        <a href="{{ route('admin.media-advocacy.reports.export', 'income-by-customer') }}" class="btn-secondary btn-sm">Export CSV</a>
    </div>

    @if ($customers->isEmpty())
        <x-empty-state icon="users" title="No customers yet" class="mt-8" />
    @else
        <x-table class="mt-6">
            <thead><tr><th>Customer</th><th>Orders</th><th>Completed Orders</th><th>Total Income</th></tr></thead>
            <tbody>
                @foreach ($customers as $customer)
                    <tr>
                        <td class="font-medium text-slate-900 dark:text-white">{{ $customer->full_name }}</td>
                        <td>{{ $customer->orders_count }}</td>
                        <td>{{ $customer->completed_orders_count }}</td>
                        <td class="font-semibold text-slate-900 dark:text-white">${{ number_format($customer->total_income ?? 0, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </x-table>
        <div class="mt-6">{{ $customers->links() }}</div>
    @endif
</x-layouts::admin>
