<x-layouts::admin :title="'Confirmed &amp; Completed Orders'">
    <x-breadcrumb :items="[['label' => 'Media Advocacy'], ['label' => 'Reports', 'url' => route('admin.media-advocacy.reports.index')], ['label' => 'Confirmed']]" class="mb-4" />
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Confirmed &amp; Completed Orders</h1>
        <a href="{{ route('admin.media-advocacy.reports.export', 'confirmed') }}" class="btn-secondary btn-sm">Export CSV</a>
    </div>

    @if ($orders->isEmpty())
        <x-empty-state icon="badge-check" title="No confirmed orders yet" class="mt-8" />
    @else
        <x-table class="mt-6">
            <thead><tr><th>Order #</th><th>Service</th><th>Customer</th><th>Status</th><th>Price</th><th>Handled By</th><th></th></tr></thead>
            <tbody>
                @foreach ($orders as $order)
                    <tr>
                        <td class="font-medium text-slate-900 dark:text-white">
                            <a href="{{ route('admin.media-advocacy.orders.show', $order) }}" class="hover:underline">#{{ $order->id }}</a>
                        </td>
                        <td>{{ $order->category->name }}</td>
                        <td>{{ $order->customer->full_name }}</td>
                        <td>
                            <x-badge :variant="$order->status === 'completed' ? 'success' : 'info'">{{ ucfirst($order->status) }}</x-badge>
                        </td>
                        <td>{{ $order->final_price ? '$' . number_format($order->final_price, 2) : '—' }}</td>
                        <td>{{ $order->handler?->full_name ?? '—' }}</td>
                        <td><a href="{{ route('admin.media-advocacy.orders.show', $order) }}" class="text-navy-700 hover:underline dark:text-navy-300">View</a></td>
                    </tr>
                @endforeach
            </tbody>
        </x-table>
        <div class="mt-6">{{ $orders->links() }}</div>
    @endif
</x-layouts::admin>
