<x-layouts::admin :title="'Admin Dashboard'">
    @vite(['resources/js/charts.js'])

    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Institution Overview</h1>
    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">A snapshot of your alumni network's health.</p>

    <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-5">
        <x-stat-card label="Total Alumni" :value="$stats['total_alumni']" icon="graduation-cap" accent="navy" />
        <x-stat-card label="Verified Alumni" :value="$stats['verified_alumni']" icon="badge-check" accent="emerald" />
        <x-stat-card label="Pending Verification" :value="$stats['pending_alumni']" icon="clock" accent="gold" />
        <x-stat-card label="Active Users (30d)" :value="$stats['active_users']" icon="users" accent="sky" />
        <x-stat-card label="New Registrations (30d)" :value="$stats['new_registrations']" icon="user-plus" accent="navy" />
        <x-stat-card label="Upcoming Events" :value="$stats['upcoming_events']" icon="calendar" accent="sky" />
        <x-stat-card label="Event Registrations" :value="$stats['event_registrations']" icon="calendar-check" accent="emerald" />
        <x-stat-card label="Active Job Listings" :value="$stats['total_jobs']" icon="briefcase" accent="gold" />
        <x-stat-card label="Total Donations" :value="'$' . number_format((float) $stats['total_donations'])" icon="dollar-sign" accent="emerald" />
        <x-stat-card label="Alumni Connections" :value="$stats['total_connections']" icon="handshake" accent="navy" />
    </div>

    <div class="mt-8 grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="card card-body lg:col-span-2">
            <h2 class="text-sm font-semibold text-slate-900 dark:text-white">Alumni Growth (6 Months)</h2>
            <div id="alumni-growth-chart" class="mt-4"></div>
        </div>

        <div class="card card-body">
            <h2 class="text-sm font-semibold text-slate-900 dark:text-white">Quick Links</h2>
            <div class="mt-3 space-y-2">
                <a href="{{ route('admin.alumni.pending') }}" class="flex items-center justify-between rounded-lg px-3 py-2.5 text-sm hover:bg-slate-50 dark:hover:bg-navy-800">
                    <span class="flex items-center gap-2 text-slate-700 dark:text-slate-200"><x-icon name="clock" class="h-4 w-4 text-gold-500" /> Pending Verifications</span>
                    <x-badge variant="warning">{{ $stats['pending_alumni'] }}</x-badge>
                </a>
                <a href="{{ route('admin.jobs.pending') }}" class="flex items-center justify-between rounded-lg px-3 py-2.5 text-sm hover:bg-slate-50 dark:hover:bg-navy-800">
                    <span class="flex items-center gap-2 text-slate-700 dark:text-slate-200"><x-icon name="briefcase" class="h-4 w-4 text-navy-600" /> Pending Jobs</span>
                </a>
                <a href="{{ route('admin.community.reports') }}" class="flex items-center justify-between rounded-lg px-3 py-2.5 text-sm hover:bg-slate-50 dark:hover:bg-navy-800">
                    <span class="flex items-center gap-2 text-slate-700 dark:text-slate-200"><x-icon name="flag" class="h-4 w-4 text-red-500" /> Content Reports</span>
                </a>
                <a href="{{ route('admin.stories.index') }}" class="flex items-center justify-between rounded-lg px-3 py-2.5 text-sm hover:bg-slate-50 dark:hover:bg-navy-800">
                    <span class="flex items-center gap-2 text-slate-700 dark:text-slate-200"><x-icon name="book-open" class="h-4 w-4 text-sky-500" /> Story Submissions</span>
                </a>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        const isDark = document.documentElement.classList.contains('dark');
        new ApexCharts(document.getElementById('alumni-growth-chart'), {
            chart: { type: 'line', height: 280, toolbar: { show: false }, foreColor: isDark ? '#cbd5e1' : '#475569' },
            series: [{ name: 'Alumni', data: @json($growthTrend->pluck('total')) }],
            xaxis: { categories: @json($growthTrend->pluck('month')) },
            colors: ['#233c6c'],
            stroke: { curve: 'smooth', width: 3 },
            dataLabels: { enabled: false },
        }).render();
    </script>
    @endpush
</x-layouts::admin>
