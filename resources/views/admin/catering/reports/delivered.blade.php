<x-layouts::admin :title="'Delivered Orders'">
    <x-breadcrumb :items="[['label' => 'Catering'], ['label' => 'Reports', 'url' => route('admin.catering.reports.index')], ['label' => 'Delivered Orders']]" class="mb-4" />
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Delivered Orders</h1>
        <a href="{{ route('admin.catering.reports.export', 'delivered') }}" class="btn-secondary btn-sm">Export CSV</a>
    </div>

    @if ($orders->isEmpty())
        <x-empty-state icon="circle-check-big" title="No delivered orders yet" class="mt-8" />
    @else
        <x-table class="mt-6">
            <thead><tr><th>Order #</th><th>Customer</th><th>Category</th><th>Total</th><th>Delivered At</th></tr></thead>
            <tbody>
                @foreach ($orders as $order)
                    <tr>
                        <td class="font-medium text-slate-900 dark:text-white">
                            <a href="{{ route('admin.catering.orders.show', $order) }}" class="hover:underline">#{{ $order->id }}</a>
                        </td>
                        <td>{{ $order->customer->full_name }}</td>
                        <td>{{ $order->category->name }}</td>
                        <td>${{ number_format($order->total_amount, 2) }}</td>
                        <td>{{ $order->delivered_at?->format('M j, Y') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </x-table>
        <div class="mt-6">{{ $orders->links() }}</div>
    @endif
</x-layouts::admin>
