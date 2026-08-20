<x-layouts::admin :title="'Seller Report'">
    <x-breadcrumb :items="[['label' => 'Marketplace'], ['label' => 'Reports', 'url' => route('admin.marketplace.reports.index')], ['label' => 'Sellers']]" class="mb-4" />

    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Seller Report</h1>
        <x-button :href="route('admin.marketplace.reports.export', 'sellers')" variant="secondary" size="sm"><x-icon name="download" class="h-4 w-4" /> Export CSV</x-button>
    </div>

    @if ($sellers->isEmpty())
        <x-empty-state icon="users" title="No sellers yet" class="mt-8" />
    @else
        <x-table class="mt-6">
            <thead><tr><th>Seller</th><th>Listings</th><th>Approved</th><th>Completed Orders</th><th>Total Commission</th></tr></thead>
            <tbody>
                @foreach ($sellers as $seller)
                    <tr>
                        <td class="font-medium text-slate-900 dark:text-white">{{ $seller->full_name }}<br><span class="text-xs font-normal text-slate-400">{{ $seller->email }}</span></td>
                        <td>{{ $seller->listings_count }}</td>
                        <td>{{ $seller->approved_listings_count }}</td>
                        <td>{{ $seller->completed_orders_count }}</td>
                        <td>${{ number_format($seller->total_commission ?? 0, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </x-table>
        <div class="mt-6">{{ $sellers->links() }}</div>
    @endif
</x-layouts::admin>
