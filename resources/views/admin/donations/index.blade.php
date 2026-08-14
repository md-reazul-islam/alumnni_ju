<x-layouts::admin :title="'Donations'">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Donations</h1>
        <div class="flex gap-2">
            <x-button :href="route('admin.donations.campaigns')" variant="secondary" size="sm">Campaigns</x-button>
            <x-button :href="route('admin.donations.reports')" size="sm">View Reports</x-button>
        </div>
    </div>

    <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
        <x-stat-card label="Total Donations" :value="'$' . number_format((float) $stats['total'])" icon="dollar-sign" accent="emerald" />
        <x-stat-card label="This Month" :value="'$' . number_format((float) $stats['this_month'])" icon="calendar" accent="navy" />
        <x-stat-card label="This Year" :value="'$' . number_format((float) $stats['this_year'])" icon="chart-bar" accent="gold" />
    </div>

    <form method="GET" class="mt-6">
        <select name="category" onchange="this.form.submit()" class="form-select max-w-xs">
            <option value="">All categories</option>
            @foreach (['scholarship', 'research', 'student_support', 'infrastructure', 'emergency_fund', 'alumni_association', 'general_fund'] as $cat)
                <option value="{{ $cat }}" @selected(request('category') === $cat)>{{ ucfirst(str_replace('_', ' ', $cat)) }}</option>
            @endforeach
        </select>
    </form>

    @if ($donations->isEmpty())
        <x-empty-state icon="dollar-sign" title="No donations recorded yet" class="mt-8" />
    @else
        <x-table class="mt-6">
            <thead><tr><th>Donor</th><th>Amount</th><th>Category</th><th>Campaign</th><th>Date</th></tr></thead>
            <tbody>
                @foreach ($donations as $donation)
                    <tr>
                        <td class="font-medium text-slate-900 dark:text-white">{{ $donation->displayName() }}</td>
                        <td>${{ number_format((float) $donation->amount, 2) }}</td>
                        <td>{{ ucfirst(str_replace('_', ' ', $donation->category)) }}</td>
                        <td>{{ $donation->campaign?->title ?? '—' }}</td>
                        <td>{{ $donation->created_at->format('M d, Y') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </x-table>
        <div class="mt-6">{{ $donations->links() }}</div>
    @endif
</x-layouts::admin>
