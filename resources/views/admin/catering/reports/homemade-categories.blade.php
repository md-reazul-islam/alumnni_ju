<x-layouts::admin :title="'Home Made Categories'">
    <x-breadcrumb :items="[['label' => 'Catering'], ['label' => 'Reports', 'url' => route('admin.catering.reports.index')], ['label' => 'Home Made Categories']]" class="mb-4" />
    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Home Made Categories</h1>

    @if ($categories->isEmpty())
        <x-empty-state icon="tags" title="No categories yet" class="mt-8" />
    @else
        <x-table class="mt-6">
            <thead><tr><th>Category</th><th>Listings</th><th>Commission %</th><th>Total Commission Earned</th></tr></thead>
            <tbody>
                @foreach ($categories as $category)
                    <tr>
                        <td class="font-medium text-slate-900 dark:text-white">{{ $category->name }}</td>
                        <td>{{ $category->listings_count }}</td>
                        <td>{{ rtrim(rtrim(number_format($category->commission_percentage, 2), '0'), '.') }}%</td>
                        <td class="font-semibold text-slate-900 dark:text-white">${{ number_format($category->total_commission, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </x-table>
    @endif
</x-layouts::admin>
