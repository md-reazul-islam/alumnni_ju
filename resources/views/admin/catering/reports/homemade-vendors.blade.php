<x-layouts::admin :title="'Home Made Vendors'">
    <x-breadcrumb :items="[['label' => 'Catering'], ['label' => 'Reports', 'url' => route('admin.catering.reports.index')], ['label' => 'Home Made Vendors']]" class="mb-4" />
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Home Made Vendors</h1>
        <a href="{{ route('admin.catering.reports.export', 'homemade-vendors') }}" class="btn-secondary btn-sm">Export CSV</a>
    </div>

    @if ($vendors->isEmpty())
        <x-empty-state icon="badge-check" title="No vendors yet" class="mt-8" />
    @else
        <x-table class="mt-6">
            <thead><tr><th>Vendor</th><th>Listings</th><th>Completed Orders</th><th>Total Sales</th></tr></thead>
            <tbody>
                @foreach ($vendors as $vendor)
                    <tr>
                        <td class="font-medium text-slate-900 dark:text-white">{{ $vendor->full_name }}</td>
                        <td>{{ $vendor->listings_count }}</td>
                        <td>{{ $vendor->completed_orders_count }}</td>
                        <td class="font-semibold text-slate-900 dark:text-white">${{ number_format($vendor->total_sales ?? 0, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </x-table>
        <div class="mt-6">{{ $vendors->links() }}</div>
    @endif
</x-layouts::admin>
