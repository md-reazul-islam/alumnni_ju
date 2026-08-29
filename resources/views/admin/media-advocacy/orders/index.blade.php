<x-layouts::admin :title="'Media Advocacy Orders'">
    <x-breadcrumb :items="[['label' => 'Media Advocacy'], ['label' => 'Orders']]" class="mb-4" />
    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Media Advocacy Orders</h1>

    @if (session('status'))
        <x-alert variant="success" class="mt-4">{{ session('status') }}</x-alert>
    @endif

    <form method="GET" class="mt-4">
        <select name="status" class="form-select max-w-xs" onchange="this.form.submit()">
            <option value="">All statuses</option>
            @foreach (['pending' => 'Pending', 'confirmed' => 'Confirmed', 'completed' => 'Completed', 'cancelled' => 'Cancelled'] as $value => $label)
                <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
            @endforeach
        </select>
    </form>

    @if ($orders->isEmpty())
        <x-empty-state icon="megaphone" title="No orders yet" class="mt-8" />
    @else
        <x-table class="mt-6">
            <thead><tr><th>Service</th><th>Customer</th><th>Status</th><th>Price</th><th></th></tr></thead>
            <tbody>
                @foreach ($orders as $order)
                    <tr>
                        <td class="font-medium text-slate-900 dark:text-white">
                            <a href="{{ route('admin.media-advocacy.orders.show', $order) }}" class="hover:underline">{{ $order->category->name }}</a>
                        </td>
                        <td>{{ $order->customer->full_name }}</td>
                        <td>
                            <x-badge :variant="match($order->status) { 'completed' => 'success', 'cancelled' => 'danger', 'confirmed' => 'info', default => 'warning' }">
                                {{ ucfirst($order->status) }}
                            </x-badge>
                        </td>
                        <td>{{ $order->final_price ? '$' . number_format($order->final_price, 2) : '—' }}</td>
                        <td><a href="{{ route('admin.media-advocacy.orders.show', $order) }}" class="text-navy-700 hover:underline dark:text-navy-300">View</a></td>
                    </tr>
                @endforeach
            </tbody>
        </x-table>
        <div class="mt-6">{{ $orders->links() }}</div>
    @endif
</x-layouts::admin>
