<x-layouts::admin :title="'Income by Service'">
    <x-breadcrumb :items="[['label' => 'Media Advocacy'], ['label' => 'Reports', 'url' => route('admin.media-advocacy.reports.index')], ['label' => 'Income by Service']]" class="mb-4" />
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Income by Service</h1>
        <a href="{{ route('admin.media-advocacy.reports.export', 'income-by-service') }}" class="btn-secondary btn-sm">Export CSV</a>
    </div>

    @if ($categories->isEmpty())
        <x-empty-state icon="tag" title="No services yet" class="mt-8" />
    @else
        <x-table class="mt-6">
            <thead><tr><th>Service</th><th>Completed Orders</th><th>Total Income</th></tr></thead>
            <tbody>
                @foreach ($categories as $category)
                    <tr>
                        <td class="font-medium text-slate-900 dark:text-white">{{ $category->name }}</td>
                        <td>{{ $category->completed_orders_count }}</td>
                        <td class="font-semibold text-slate-900 dark:text-white">${{ number_format($category->total_income ?? 0, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </x-table>
    @endif
</x-layouts::admin>
