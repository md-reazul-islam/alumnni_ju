<x-layouts::admin :title="'Reports & Analytics'">
    @vite(['resources/js/charts.js'])

    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Reports &amp; Analytics</h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Institution-wide trends across the alumni network.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <x-button :href="route('admin.reports.export', 'demographics')" variant="secondary" size="sm"><x-icon name="download" class="h-4 w-4" /> Demographics</x-button>
            <x-button :href="route('admin.reports.export', 'events')" variant="secondary" size="sm"><x-icon name="download" class="h-4 w-4" /> Events</x-button>
            <x-button :href="route('admin.reports.export', 'donations')" variant="secondary" size="sm"><x-icon name="download" class="h-4 w-4" /> Donations</x-button>
            <x-button :href="route('admin.reports.export', 'mentorship')" variant="secondary" size="sm"><x-icon name="download" class="h-4 w-4" /> Mentorship</x-button>
        </div>
    </div>

    <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-2">
        <div class="card card-body lg:col-span-2">
            <h2 class="text-sm font-semibold text-slate-900 dark:text-white">Alumni Growth</h2>
            <div id="growth-chart" class="mt-4"></div>
        </div>

        <div class="card card-body">
            <h2 class="text-sm font-semibold text-slate-900 dark:text-white">Alumni by Department</h2>
            <div id="department-chart" class="mt-4"></div>
        </div>

        <div class="card card-body">
            <h2 class="text-sm font-semibold text-slate-900 dark:text-white">Alumni by Graduation Year</h2>
            <div id="graduation-chart" class="mt-4"></div>
        </div>

        <div class="card card-body">
            <h2 class="text-sm font-semibold text-slate-900 dark:text-white">Alumni by Country</h2>
            <div id="country-chart" class="mt-4"></div>
        </div>

        <div class="card card-body">
            <h2 class="text-sm font-semibold text-slate-900 dark:text-white">Employment Industries</h2>
            <div id="industry-chart" class="mt-4"></div>
        </div>

        <div class="card card-body lg:col-span-2">
            <h2 class="text-sm font-semibold text-slate-900 dark:text-white">Event Participation</h2>
            <div id="event-chart" class="mt-4"></div>
        </div>
    </div>

    @push('scripts')
    <script>
        fetch('{{ route('admin.reports.chart-data') }}', { headers: { Accept: 'application/json' } })
            .then((r) => r.json())
            .then((data) => {
                const isDark = document.documentElement.classList.contains('dark');
                const textColor = isDark ? '#cbd5e1' : '#475569';
                const palette = ['#233c6c', '#d4941f', '#0ea5e9', '#10b981', '#ef4444', '#8b5cf6', '#64748b', '#ec4899'];

                new ApexCharts(document.getElementById('growth-chart'), {
                    chart: { type: 'line', height: 260, toolbar: { show: false }, foreColor: textColor },
                    series: [{ name: 'Alumni', data: data.growth.map((g) => g.total) }],
                    xaxis: { categories: data.growth.map((g) => g.month) },
                    colors: ['#233c6c'],
                    stroke: { curve: 'smooth', width: 3 },
                    dataLabels: { enabled: false },
                }).render();

                new ApexCharts(document.getElementById('department-chart'), {
                    chart: { type: 'bar', height: 260, toolbar: { show: false }, foreColor: textColor },
                    series: [{ name: 'Alumni', data: data.byDepartment.map((d) => d.total) }],
                    xaxis: { categories: data.byDepartment.map((d) => d.label) },
                    colors: ['#d4941f'],
                    plotOptions: { bar: { horizontal: true, borderRadius: 4 } },
                    dataLabels: { enabled: false },
                }).render();

                new ApexCharts(document.getElementById('graduation-chart'), {
                    chart: { type: 'bar', height: 260, toolbar: { show: false }, foreColor: textColor },
                    series: [{ name: 'Alumni', data: data.byGraduationYear.map((g) => g.total) }],
                    xaxis: { categories: data.byGraduationYear.map((g) => g.graduation_year) },
                    colors: ['#0ea5e9'],
                    plotOptions: { bar: { borderRadius: 4 } },
                    dataLabels: { enabled: false },
                }).render();

                new ApexCharts(document.getElementById('country-chart'), {
                    chart: { type: 'bar', height: 260, toolbar: { show: false }, foreColor: textColor },
                    series: [{ name: 'Alumni', data: data.byCountry.map((c) => c.total) }],
                    xaxis: { categories: data.byCountry.map((c) => c.country) },
                    colors: ['#10b981'],
                    plotOptions: { bar: { horizontal: true, borderRadius: 4 } },
                    dataLabels: { enabled: false },
                }).render();

                new ApexCharts(document.getElementById('industry-chart'), {
                    chart: { type: 'donut', height: 260, foreColor: textColor },
                    series: data.byIndustry.map((i) => i.total),
                    labels: data.byIndustry.map((i) => i.industry),
                    colors: palette,
                    legend: { position: 'bottom' },
                }).render();

                new ApexCharts(document.getElementById('event-chart'), {
                    chart: { type: 'bar', height: 260, toolbar: { show: false }, foreColor: textColor },
                    series: [{ name: 'Registrations', data: data.eventParticipation.map((e) => e.total) }],
                    xaxis: { categories: data.eventParticipation.map((e) => e.label) },
                    colors: ['#233c6c'],
                    plotOptions: { bar: { borderRadius: 4 } },
                    dataLabels: { enabled: false },
                }).render();
            });
    </script>
    @endpush
</x-layouts::admin>
