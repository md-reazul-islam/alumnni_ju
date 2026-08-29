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

    <h2 class="mt-10 text-lg font-bold text-slate-900 dark:text-white">Module Reports</h2>
    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">A quick glance at every feature area — open a module's full report for the deep dive.</p>

    <div class="mt-4 grid grid-cols-1 gap-6 lg:grid-cols-2">
        <div class="card card-body">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-semibold text-slate-900 dark:text-white">Career &amp; Jobs</h3>
                <a href="{{ route('admin.jobs.index') }}" class="text-xs font-medium text-navy-700 hover:underline dark:text-navy-300">View All &rarr;</a>
            </div>
            <div class="mt-3 grid grid-cols-2 gap-3 sm:grid-cols-4">
                <div><p class="text-xs text-slate-400">Approved</p><p class="text-lg font-bold text-slate-900 dark:text-white">{{ $modules['career']['stats']['approved'] }}</p></div>
                <div><p class="text-xs text-slate-400">Pending</p><p class="text-lg font-bold text-slate-900 dark:text-white">{{ $modules['career']['stats']['pending'] }}</p></div>
                <div><p class="text-xs text-slate-400">Applications</p><p class="text-lg font-bold text-slate-900 dark:text-white">{{ $modules['career']['stats']['applications'] }}</p></div>
                <div><p class="text-xs text-slate-400">Total Jobs</p><p class="text-lg font-bold text-slate-900 dark:text-white">{{ $modules['career']['stats']['total'] }}</p></div>
            </div>
            <div id="career-chart" class="mt-4"></div>
        </div>

        <div class="card card-body">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-semibold text-slate-900 dark:text-white">Marketplace</h3>
                <a href="{{ route('admin.marketplace.reports.index') }}" class="text-xs font-medium text-navy-700 hover:underline dark:text-navy-300">View All &rarr;</a>
            </div>
            <div class="mt-3 grid grid-cols-2 gap-3 sm:grid-cols-4">
                <div><p class="text-xs text-slate-400">Listings</p><p class="text-lg font-bold text-slate-900 dark:text-white">{{ $modules['marketplace']['stats']['total_listings'] }}</p></div>
                <div><p class="text-xs text-slate-400">Pending</p><p class="text-lg font-bold text-slate-900 dark:text-white">{{ $modules['marketplace']['stats']['pending_listings'] }}</p></div>
                <div><p class="text-xs text-slate-400">Completed Orders</p><p class="text-lg font-bold text-slate-900 dark:text-white">{{ $modules['marketplace']['stats']['completed_orders'] }}</p></div>
                <div><p class="text-xs text-slate-400">Commission</p><p class="text-lg font-bold text-slate-900 dark:text-white">${{ number_format($modules['marketplace']['stats']['total_commission']) }}</p></div>
            </div>
            <div id="marketplace-chart" class="mt-4"></div>
        </div>

        <div class="card card-body">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-semibold text-slate-900 dark:text-white">Matrimony</h3>
                <a href="{{ route('admin.matrimony.reports.index') }}" class="text-xs font-medium text-navy-700 hover:underline dark:text-navy-300">View All &rarr;</a>
            </div>
            <div class="mt-3 grid grid-cols-2 gap-3 sm:grid-cols-4">
                <div><p class="text-xs text-slate-400">Approved Profiles</p><p class="text-lg font-bold text-slate-900 dark:text-white">{{ $modules['matrimony']['stats']['approved_profiles'] }}</p></div>
                <div><p class="text-xs text-slate-400">Pending</p><p class="text-lg font-bold text-slate-900 dark:text-white">{{ $modules['matrimony']['stats']['pending_profiles'] }}</p></div>
                <div><p class="text-xs text-slate-400">Interests</p><p class="text-lg font-bold text-slate-900 dark:text-white">{{ $modules['matrimony']['stats']['total_interests'] }}</p></div>
                <div><p class="text-xs text-slate-400">Accepted</p><p class="text-lg font-bold text-slate-900 dark:text-white">{{ $modules['matrimony']['stats']['accepted_interests'] }}</p></div>
            </div>
            <div id="matrimony-chart" class="mt-4"></div>
        </div>

        <div class="card card-body">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-semibold text-slate-900 dark:text-white">Catering</h3>
                <a href="{{ route('admin.catering.reports.index') }}" class="text-xs font-medium text-navy-700 hover:underline dark:text-navy-300">View All &rarr;</a>
            </div>
            <div class="mt-3 grid grid-cols-2 gap-3 sm:grid-cols-4">
                <div><p class="text-xs text-slate-400">Total Orders</p><p class="text-lg font-bold text-slate-900 dark:text-white">{{ $modules['catering']['stats']['total_orders'] }}</p></div>
                <div><p class="text-xs text-slate-400">Delivered</p><p class="text-lg font-bold text-slate-900 dark:text-white">{{ $modules['catering']['stats']['delivered_orders'] }}</p></div>
                <div><p class="text-xs text-slate-400">Revenue</p><p class="text-lg font-bold text-slate-900 dark:text-white">${{ number_format($modules['catering']['stats']['total_revenue']) }}</p></div>
                <div><p class="text-xs text-slate-400">Homemade Done</p><p class="text-lg font-bold text-slate-900 dark:text-white">{{ $modules['catering']['stats']['homemade_completed'] }}</p></div>
            </div>
            <div id="catering-chart" class="mt-4"></div>
        </div>

        <div class="card card-body">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-semibold text-slate-900 dark:text-white">Media Advocacy</h3>
                <a href="{{ route('admin.media-advocacy.reports.index') }}" class="text-xs font-medium text-navy-700 hover:underline dark:text-navy-300">View All &rarr;</a>
            </div>
            <div class="mt-3 grid grid-cols-2 gap-3 sm:grid-cols-4">
                <div><p class="text-xs text-slate-400">Total Orders</p><p class="text-lg font-bold text-slate-900 dark:text-white">{{ $modules['media_advocacy']['stats']['total_orders'] }}</p></div>
                <div><p class="text-xs text-slate-400">Pending</p><p class="text-lg font-bold text-slate-900 dark:text-white">{{ $modules['media_advocacy']['stats']['pending_orders'] }}</p></div>
                <div><p class="text-xs text-slate-400">Completed</p><p class="text-lg font-bold text-slate-900 dark:text-white">{{ $modules['media_advocacy']['stats']['completed_orders'] }}</p></div>
                <div><p class="text-xs text-slate-400">Income</p><p class="text-lg font-bold text-slate-900 dark:text-white">${{ number_format($modules['media_advocacy']['stats']['total_income']) }}</p></div>
            </div>
            <div id="media-advocacy-chart" class="mt-4"></div>
        </div>

        <div class="card card-body">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-semibold text-slate-900 dark:text-white">Mentorship</h3>
                <a href="{{ route('admin.mentorship.requests') }}" class="text-xs font-medium text-navy-700 hover:underline dark:text-navy-300">View All &rarr;</a>
            </div>
            <div class="mt-3 grid grid-cols-2 gap-3 sm:grid-cols-4">
                <div><p class="text-xs text-slate-400">Active Mentors</p><p class="text-lg font-bold text-slate-900 dark:text-white">{{ $modules['mentorship']['stats']['active_mentors'] }}</p></div>
                <div><p class="text-xs text-slate-400">Active Pairs</p><p class="text-lg font-bold text-slate-900 dark:text-white">{{ $modules['mentorship']['stats']['active_mentorships'] }}</p></div>
                <div><p class="text-xs text-slate-400">Pending Requests</p><p class="text-lg font-bold text-slate-900 dark:text-white">{{ $modules['mentorship']['stats']['pending_requests'] }}</p></div>
                <div><p class="text-xs text-slate-400">Completed</p><p class="text-lg font-bold text-slate-900 dark:text-white">{{ $modules['mentorship']['stats']['completed_mentorships'] }}</p></div>
            </div>
            <div id="mentorship-chart" class="mt-4"></div>
        </div>

        <div class="card card-body">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-semibold text-slate-900 dark:text-white">Library</h3>
                <a href="{{ route('admin.library.index') }}" class="text-xs font-medium text-navy-700 hover:underline dark:text-navy-300">View All &rarr;</a>
            </div>
            <div class="mt-3 grid grid-cols-2 gap-3 sm:grid-cols-4">
                <div><p class="text-xs text-slate-400">Total Books</p><p class="text-lg font-bold text-slate-900 dark:text-white">{{ $modules['library']['stats']['total_books'] }}</p></div>
                <div><p class="text-xs text-slate-400">Approved</p><p class="text-lg font-bold text-slate-900 dark:text-white">{{ $modules['library']['stats']['approved_books'] }}</p></div>
                <div><p class="text-xs text-slate-400">Pending Requests</p><p class="text-lg font-bold text-slate-900 dark:text-white">{{ $modules['library']['stats']['pending_requests'] }}</p></div>
                <div><p class="text-xs text-slate-400">Overdue</p><p class="text-lg font-bold text-slate-900 dark:text-white">{{ $modules['library']['stats']['overdue'] }}</p></div>
            </div>
            <div id="library-chart" class="mt-4"></div>
        </div>

        <div class="card card-body">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-semibold text-slate-900 dark:text-white">Donations</h3>
                <a href="{{ route('admin.donations.reports') }}" class="text-xs font-medium text-navy-700 hover:underline dark:text-navy-300">View All &rarr;</a>
            </div>
            <div class="mt-3 grid grid-cols-2 gap-3 sm:grid-cols-4">
                <div><p class="text-xs text-slate-400">Total Raised</p><p class="text-lg font-bold text-slate-900 dark:text-white">${{ number_format($modules['donations']['stats']['total']) }}</p></div>
                <div><p class="text-xs text-slate-400">This Month</p><p class="text-lg font-bold text-slate-900 dark:text-white">${{ number_format($modules['donations']['stats']['this_month']) }}</p></div>
                <div><p class="text-xs text-slate-400">Active Campaigns</p><p class="text-lg font-bold text-slate-900 dark:text-white">{{ $modules['donations']['stats']['active_campaigns'] }}</p></div>
                <div><p class="text-xs text-slate-400">Donations</p><p class="text-lg font-bold text-slate-900 dark:text-white">{{ $modules['donations']['stats']['total_donations'] }}</p></div>
            </div>
            <div id="donations-chart" class="mt-4"></div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const isDark = document.documentElement.classList.contains('dark');
            new ApexCharts(document.getElementById('alumni-growth-chart'), {
                chart: { type: 'line', height: 280, toolbar: { show: false }, foreColor: isDark ? '#cbd5e1' : '#475569' },
                series: [{ name: 'Alumni', data: @json($growthTrend->pluck('total')) }],
                xaxis: { categories: @json($growthTrend->pluck('month')) },
                colors: ['#233c6c'],
                stroke: { curve: 'smooth', width: 3 },
                dataLabels: { enabled: false },
            }).render();

            const textColor = isDark ? '#cbd5e1' : '#475569';
            const palette = ['#233c6c', '#d4941f', '#0ea5e9', '#10b981', '#ef4444', '#8b5cf6', '#64748b', '#ec4899'];

            new ApexCharts(document.getElementById('career-chart'), {
                chart: { type: 'bar', height: 220, toolbar: { show: false }, foreColor: textColor },
                series: [{ name: 'Jobs', data: @json($modules['career']['chart']->pluck('total')) }],
                xaxis: { categories: @json($modules['career']['chart']->pluck('industry')) },
                colors: ['#233c6c'],
                plotOptions: { bar: { horizontal: true, borderRadius: 4 } },
                dataLabels: { enabled: false },
                noData: { text: 'No approved jobs with an industry set yet' },
            }).render();

            new ApexCharts(document.getElementById('marketplace-chart'), {
                chart: { type: 'bar', height: 220, toolbar: { show: false }, foreColor: textColor },
                series: [{ name: 'Listings', data: @json($modules['marketplace']['chart']->pluck('listings_count')) }],
                xaxis: { categories: @json($modules['marketplace']['chart']->pluck('name')) },
                colors: ['#d4941f'],
                plotOptions: { bar: { borderRadius: 4 } },
                dataLabels: { enabled: false },
                noData: { text: 'No categories yet' },
            }).render();

            new ApexCharts(document.getElementById('matrimony-chart'), {
                chart: { type: 'donut', height: 220, foreColor: textColor },
                series: @json(array_values($modules['matrimony']['chart'])),
                labels: ['Male', 'Female', 'Other'],
                colors: palette,
                legend: { position: 'bottom' },
                noData: { text: 'No approved profiles yet' },
            }).render();

            new ApexCharts(document.getElementById('catering-chart'), {
                chart: { type: 'area', height: 220, toolbar: { show: false }, foreColor: textColor },
                series: [{ name: 'Revenue', data: @json($modules['catering']['chart']->pluck('revenue')) }],
                xaxis: { categories: @json($modules['catering']['chart']->pluck('month')) },
                colors: ['#0ea5e9'],
                stroke: { curve: 'smooth', width: 2 },
                dataLabels: { enabled: false },
                yaxis: { labels: { formatter: (v) => '$' + Math.round(v) } },
            }).render();

            new ApexCharts(document.getElementById('media-advocacy-chart'), {
                chart: { type: 'bar', height: 220, toolbar: { show: false }, foreColor: textColor },
                series: [{ name: 'Income', data: @json($modules['media_advocacy']['chart']->pluck('total_income')->map(fn ($v) => (float) ($v ?? 0))) }],
                xaxis: { categories: @json($modules['media_advocacy']['chart']->pluck('name')) },
                colors: ['#10b981'],
                plotOptions: { bar: { horizontal: true, borderRadius: 4 } },
                dataLabels: { enabled: false },
                noData: { text: 'No services yet' },
            }).render();

            new ApexCharts(document.getElementById('mentorship-chart'), {
                chart: { type: 'donut', height: 220, foreColor: textColor },
                series: @json(array_values($modules['mentorship']['chart'])),
                labels: ['Pending', 'Accepted', 'Rejected', 'Completed'],
                colors: palette,
                legend: { position: 'bottom' },
                noData: { text: 'No mentorship requests yet' },
            }).render();

            new ApexCharts(document.getElementById('library-chart'), {
                chart: { type: 'bar', height: 220, toolbar: { show: false }, foreColor: textColor },
                series: [{ name: 'Requests', data: @json(array_values($modules['library']['chart'])) }],
                xaxis: { categories: ['Pending', 'Approved', 'Handed Over', 'Returned', 'Rejected'] },
                colors: ['#8b5cf6'],
                plotOptions: { bar: { borderRadius: 4 } },
                dataLabels: { enabled: false },
            }).render();

            new ApexCharts(document.getElementById('donations-chart'), {
                chart: { type: 'area', height: 220, toolbar: { show: false }, foreColor: textColor },
                series: [{ name: 'Donations', data: @json($modules['donations']['chart']->pluck('total')) }],
                xaxis: { categories: @json($modules['donations']['chart']->pluck('month')) },
                colors: ['#ec4899'],
                stroke: { curve: 'smooth', width: 2 },
                dataLabels: { enabled: false },
                yaxis: { labels: { formatter: (v) => '$' + Math.round(v) } },
            }).render();
        });
    </script>
    @endpush
</x-layouts::admin>
