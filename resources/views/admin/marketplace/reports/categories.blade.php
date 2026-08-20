<x-layouts::admin :title="'Category Report'">
    <x-breadcrumb :items="[['label' => 'Marketplace'], ['label' => 'Reports', 'url' => route('admin.marketplace.reports.index')], ['label' => 'Categories']]" class="mb-4" />

    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Category Report</h1>
        <x-button :href="route('admin.marketplace.reports.export', 'categories')" variant="secondary" size="sm"><x-icon name="download" class="h-4 w-4" /> Export CSV</x-button>
    </div>

    @if ($categories->isEmpty())
        <x-empty-state icon="tags" title="No categories yet" class="mt-8" />
    @else
        <x-table class="mt-6">
            <thead><tr><th>Category</th><th>Commission %</th><th>Listings</th><th>Approved</th><th>Completed Orders</th><th>Total Commission</th></tr></thead>
            <tbody>
                @foreach ($categories as $category)
                    <tr>
                        <td class="font-medium text-slate-900 dark:text-white">{{ $category->name }}</td>
                        <td>{{ rtrim(rtrim(number_format($category->commission_percentage, 2), '0'), '.') }}%</td>
                        <td>{{ $category->listings_count }}</td>
                        <td>{{ $category->approved_listings_count }}</td>
                        <td>{{ $category->completed_orders_count }}</td>
                        <td>${{ number_format($category->total_commission, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </x-table>
    @endif
</x-layouts::admin>
