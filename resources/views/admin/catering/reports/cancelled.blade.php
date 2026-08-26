<x-layouts::admin :title="'Cancelled Orders'">
    <x-breadcrumb :items="[['label' => 'Catering'], ['label' => 'Reports', 'url' => route('admin.catering.reports.index')], ['label' => 'Cancelled Orders']]" class="mb-4" />
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Cancelled Orders</h1>
        <a href="{{ route('admin.catering.reports.export', 'cancelled') }}" class="btn-secondary btn-sm">Export CSV</a>
    </div>

    @if ($orders->isEmpty())
        <x-empty-state icon="ban" title="No cancelled orders" class="mt-8" />
    @else
        <x-table class="mt-6">
            <thead><tr><th>Order #</th><th>Customer</th><th>Category</th><th>Cancelled By</th><th>Reason</th><th>Cancelled At</th></tr></thead>
            <tbody>
                @foreach ($orders as $order)
                    <tr>
                        <td class="font-medium text-slate-900 dark:text-white">
                            <a href="{{ route('admin.catering.orders.show', $order) }}" class="hover:underline">#{{ $order->id }}</a>
                        </td>
                        <td>{{ $order->customer->full_name }}</td>
                        <td>{{ $order->category->name }}</td>
                        <td>{{ $order->canceller?->full_name ?? '—' }}</td>
                        <td class="max-w-xs truncate" title="{{ $order->cancellation_reason }}">{{ $order->cancellation_reason }}</td>
                        <td>{{ $order->cancelled_at?->format('M j, Y') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </x-table>
        <div class="mt-6">{{ $orders->links() }}</div>
    @endif
</x-layouts::admin>
