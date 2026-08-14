<x-layouts::admin :title="'Donation Reports'">
    @vite(['resources/js/charts.js'])

    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Donation Reports</h1>
    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Trends and breakdowns across all completed donations.</p>

    <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-2">
        <div class="card card-body">
            <h2 class="text-sm font-semibold text-slate-900 dark:text-white">Donation Trends (12 Months)</h2>
            <div id="donation-trend-chart" class="mt-4"></div>
        </div>
        <div class="card card-body">
            <h2 class="text-sm font-semibold text-slate-900 dark:text-white">Donations by Category</h2>
            <div id="donation-category-chart" class="mt-4"></div>
        </div>
    </div>

    @push('scripts')
    <script>
        fetch('{{ route('admin.donations.reports.chart-data') }}', { headers: { Accept: 'application/json' } })
            .then((r) => r.json())
            .then((data) => {
                const isDark = document.documentElement.classList.contains('dark');
                const textColor = isDark ? '#cbd5e1' : '#475569';

                new ApexCharts(document.getElementById('donation-trend-chart'), {
                    chart: { type: 'area', height: 280, toolbar: { show: false }, foreColor: textColor },
                    series: [{ name: 'Donations', data: data.trend.map((t) => t.total) }],
                    xaxis: { categories: data.trend.map((t) => t.month) },
                    colors: ['#233c6c'],
                    dataLabels: { enabled: false },
                    stroke: { curve: 'smooth', width: 2 },
                    fill: { type: 'gradient', gradient: { opacityFrom: 0.4, opacityTo: 0.05 } },
                    yaxis: { labels: { formatter: (val) => '$' + val.toLocaleString() } },
                }).render();

                new ApexCharts(document.getElementById('donation-category-chart'), {
                    chart: { type: 'donut', height: 280, foreColor: textColor },
                    series: data.byCategory.map((c) => parseFloat(c.total)),
                    labels: data.byCategory.map((c) => c.category.replace('_', ' ')),
                    colors: ['#233c6c', '#d4941f', '#0ea5e9', '#10b981', '#ef4444', '#8b5cf6', '#64748b'],
                    legend: { position: 'bottom' },
                }).render();
            });
    </script>
    @endpush
</x-layouts::admin>
