<x-layouts::admin :title="'Home Made Orders'">
    <x-breadcrumb :items="[['label' => 'Catering'], ['label' => 'Reports', 'url' => route('admin.catering.reports.index')], ['label' => 'Home Made Orders']]" class="mb-4" />
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Home Made Orders</h1>
        <a href="{{ route('admin.catering.reports.export', 'homemade-orders') }}" class="btn-secondary btn-sm">Export CSV</a>
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
        <x-empty-state icon="cooking-pot" title="No orders yet" class="mt-8" />
    @else
        <x-table class="mt-6">
            <thead><tr><th>Listing</th><th>Qty</th><th>Buyer</th><th>Seller</th><th>Status</th><th>Final Price</th><th>Commission</th></tr></thead>
            <tbody>
                @foreach ($orders as $order)
                    <tr>
                        <td class="font-medium text-slate-900 dark:text-white">
                            <a href="{{ route('admin.catering.homemade-orders.show', $order) }}" class="hover:underline">{{ $order->listing->title }}</a>
                        </td>
                        <td>{{ $order->quantity }}</td>
                        <td>{{ $order->buyer->full_name }}</td>
                        <td>{{ $order->seller->full_name }}</td>
                        <td>
                            <x-badge :variant="match($order->status) { 'completed' => 'success', 'cancelled' => 'danger', 'ongoing' => 'info', default => 'warning' }">
                                {{ ucfirst($order->status) }}
                            </x-badge>
                        </td>
                        <td>{{ $order->final_price ? '$' . number_format($order->final_price, 2) : '—' }}</td>
                        <td>{{ $order->commission_amount ? '$' . number_format($order->commission_amount, 2) : '—' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </x-table>
        <div class="mt-6">{{ $orders->links() }}</div>
    @endif
</x-layouts::admin>
