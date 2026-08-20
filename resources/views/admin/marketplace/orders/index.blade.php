<x-layouts::admin :title="'Marketplace Orders'">
    <x-breadcrumb :items="[['label' => 'Marketplace'], ['label' => 'Orders']]" class="mb-4" />
    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Marketplace Orders</h1>

    @if (session('status'))
        <x-alert variant="success" class="mt-4">{{ session('status') }}</x-alert>
    @endif

    <form method="GET" class="mt-4">
        <select name="status" class="form-select max-w-xs" onchange="this.form.submit()">
            <option value="">All statuses</option>
            @foreach (['pending' => 'Pending', 'ongoing' => 'Ongoing', 'completed' => 'Completed', 'cancelled' => 'Cancelled'] as $value => $label)
                <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
            @endforeach
        </select>
    </form>

    @if ($orders->isEmpty())
        <x-empty-state icon="shopping-bag" title="No orders yet" class="mt-8" />
    @else
        <x-table class="mt-6">
            <thead><tr><th>Listing</th><th>Buyer</th><th>Seller</th><th>Status</th><th>Commission</th><th></th></tr></thead>
            <tbody>
                @foreach ($orders as $order)
                    <tr>
                        <td class="font-medium text-slate-900 dark:text-white">
                            <a href="{{ route('admin.marketplace.orders.show', $order) }}" class="hover:underline">{{ $order->listing->title }}</a>
                        </td>
                        <td>{{ $order->buyer->full_name }}</td>
                        <td>{{ $order->seller->full_name }}</td>
                        <td>
                            <x-badge :variant="match($order->status) { 'completed' => 'success', 'cancelled' => 'danger', 'ongoing' => 'info', default => 'warning' }">
                                {{ ucfirst($order->status) }}
                            </x-badge>
                        </td>
                        <td>{{ $order->commission_amount ? '$' . number_format($order->commission_amount, 2) : '—' }}</td>
                        <td><a href="{{ route('admin.marketplace.orders.show', $order) }}" class="text-navy-700 hover:underline dark:text-navy-300">View</a></td>
                    </tr>
                @endforeach
            </tbody>
        </x-table>
        <div class="mt-6">{{ $orders->links() }}</div>
    @endif
</x-layouts::admin>
