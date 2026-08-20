<x-layouts::admin :title="'Order Report'">
    <x-breadcrumb :items="[['label' => 'Marketplace'], ['label' => 'Reports', 'url' => route('admin.marketplace.reports.index')], ['label' => 'Orders']]" class="mb-4" />

    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Order Report</h1>
        <x-button :href="route('admin.marketplace.reports.export', 'orders')" variant="secondary" size="sm"><x-icon name="download" class="h-4 w-4" /> Export CSV</x-button>
    </div>

    <form method="GET" class="mt-4">
        <select name="status" class="form-select max-w-xs" onchange="this.form.submit()">
            <option value="">All statuses</option>
            @foreach (['pending' => 'Pending', 'ongoing' => 'Ongoing', 'completed' => 'Completed', 'cancelled' => 'Cancelled'] as $value => $label)
                <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
            @endforeach
        </select>
    </form>

    @if ($orders->isEmpty())
        <x-empty-state icon="clipboard-list" title="No orders found" class="mt-8" />
    @else
        <x-table class="mt-6">
            <thead><tr><th>Listing</th><th>Category</th><th>Buyer</th><th>Seller</th><th>Status</th><th>Final Price</th><th>Commission</th><th>Date</th></tr></thead>
            <tbody>
                @foreach ($orders as $order)
                    <tr>
                        <td class="font-medium text-slate-900 dark:text-white">
                            <a href="{{ route('admin.marketplace.orders.show', $order) }}" class="hover:underline">{{ $order->listing->title }}</a>
                        </td>
                        <td>{{ $order->listing->category->name }}</td>
                        <td>{{ $order->buyer->full_name }}</td>
                        <td>{{ $order->seller->full_name }}</td>
                        <td>
                            <x-badge :variant="match($order->status) { 'completed' => 'success', 'cancelled' => 'danger', 'ongoing' => 'info', default => 'warning' }">
                                {{ ucfirst($order->status) }}
                            </x-badge>
                        </td>
                        <td>{{ $order->final_price ? '$' . number_format($order->final_price, 2) : '—' }}</td>
                        <td>{{ $order->commission_amount ? '$' . number_format($order->commission_amount, 2) : '—' }}</td>
                        <td>{{ $order->created_at->format('M j, Y') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </x-table>
        <div class="mt-6">{{ $orders->links() }}</div>
    @endif
</x-layouts::admin>
