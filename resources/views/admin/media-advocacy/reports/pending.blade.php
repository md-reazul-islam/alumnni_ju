<x-layouts::admin :title="'Pending Requests'">
    <x-breadcrumb :items="[['label' => 'Media Advocacy'], ['label' => 'Reports', 'url' => route('admin.media-advocacy.reports.index')], ['label' => 'Pending']]" class="mb-4" />
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Pending Requests</h1>
        <a href="{{ route('admin.media-advocacy.reports.export', 'pending') }}" class="btn-secondary btn-sm">Export CSV</a>
    </div>

    @if ($orders->isEmpty())
        <x-empty-state icon="clock" title="No pending requests" class="mt-8" />
    @else
        <x-table class="mt-6">
            <thead><tr><th>Order #</th><th>Service</th><th>Customer</th><th>Requested</th><th></th></tr></thead>
            <tbody>
                @foreach ($orders as $order)
                    <tr>
                        <td class="font-medium text-slate-900 dark:text-white">
                            <a href="{{ route('admin.media-advocacy.orders.show', $order) }}" class="hover:underline">#{{ $order->id }}</a>
                        </td>
                        <td>{{ $order->category->name }}</td>
                        <td>{{ $order->customer->full_name }}</td>
                        <td>{{ $order->created_at->format('M j, Y') }}</td>
                        <td><a href="{{ route('admin.media-advocacy.orders.show', $order) }}" class="text-navy-700 hover:underline dark:text-navy-300">View</a></td>
                    </tr>
                @endforeach
            </tbody>
        </x-table>
        <div class="mt-6">{{ $orders->links() }}</div>
    @endif
</x-layouts::admin>
