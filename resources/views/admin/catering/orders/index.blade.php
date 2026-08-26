<x-layouts::admin :title="'Catering Orders'">
    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Catering Orders</h1>

    @if (session('status'))
        <x-alert variant="success" class="mt-4">{{ session('status') }}</x-alert>
    @endif

    <form method="GET" class="mt-4 flex gap-3">
        <x-select label="" name="status" placeholder="All statuses" :options="['submitted' => 'Submitted', 'priced' => 'Priced', 'accepted' => 'Accepted', 'declined' => 'Declined', 'delivered' => 'Delivered', 'cancelled' => 'Cancelled']" :selected="request('status')" />
        <div class="flex items-end"><x-button type="submit" variant="secondary" size="sm">Filter</x-button></div>
    </form>

    @if ($orders->isEmpty())
        <x-empty-state icon="utensils" title="No catering orders yet" class="mt-8" />
    @else
        <x-table class="mt-6">
            <thead><tr><th>#</th><th>Customer</th><th>Category</th><th>Event Date</th><th>Total</th><th>Status</th><th></th></tr></thead>
            <tbody>
                @foreach ($orders as $order)
                    <tr>
                        <td>#{{ $order->id }}</td>
                        <td class="font-medium text-slate-900 dark:text-white">{{ $order->customer->full_name }}</td>
                        <td>{{ $order->category->name }}</td>
                        <td>{{ $order->event_date->format('M j, Y') }}</td>
                        <td>{{ $order->total_amount ? '$' . number_format($order->total_amount, 2) : '—' }}</td>
                        <td>
                            <x-badge :variant="match($order->status) { 'accepted', 'delivered' => 'success', 'declined', 'cancelled' => 'danger', default => 'warning' }">
                                {{ ucfirst($order->status) }}
                            </x-badge>
                        </td>
                        <td><a href="{{ route('admin.catering.orders.show', $order) }}" class="text-navy-700 hover:underline dark:text-navy-300">Review</a></td>
                    </tr>
                @endforeach
            </tbody>
        </x-table>
        <div class="mt-6">{{ $orders->links() }}</div>
    @endif
</x-layouts::admin>
