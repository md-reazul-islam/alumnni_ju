<x-layouts::admin :title="'Top Customers'">
    <x-breadcrumb :items="[['label' => 'Catering'], ['label' => 'Reports', 'url' => route('admin.catering.reports.index')], ['label' => 'Top Customers']]" class="mb-4" />
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Top Customers</h1>
        <a href="{{ route('admin.catering.reports.export', 'top-customers') }}" class="btn-secondary btn-sm">Export CSV</a>
    </div>

    @if ($customers->isEmpty())
        <x-empty-state icon="users" title="No customers yet" class="mt-8" />
    @else
        <x-table class="mt-6">
            <thead><tr><th>Customer</th><th>Orders</th><th>Total Spent</th></tr></thead>
            <tbody>
                @foreach ($customers as $customer)
                    <tr>
                        <td class="font-medium text-slate-900 dark:text-white">{{ $customer->full_name }}</td>
                        <td>{{ $customer->orders_count }}</td>
                        <td class="font-semibold text-slate-900 dark:text-white">${{ number_format($customer->total_spent ?? 0, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </x-table>
        <div class="mt-6">{{ $customers->links() }}</div>
    @endif
</x-layouts::admin>
