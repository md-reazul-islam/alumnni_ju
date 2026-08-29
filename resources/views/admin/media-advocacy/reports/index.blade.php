<x-layouts::admin :title="'Media Advocacy Reports'">
    <x-breadcrumb :items="[['label' => 'Media Advocacy'], ['label' => 'Reports']]" class="mb-4" />
    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Media Advocacy Reports</h1>

    <div class="mt-6 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
        <x-stat-card label="Total Orders" :value="$summary['total_orders']" icon="clipboard-list" accent="sky" />
        <x-stat-card label="Pending" :value="$summary['pending_orders']" icon="clock" accent="gold" />
        <x-stat-card label="Confirmed" :value="$summary['confirmed_orders']" icon="badge-check" accent="emerald" />
        <x-stat-card label="Completed" :value="$summary['completed_orders']" icon="circle-check-big" accent="emerald" />
        <x-stat-card label="Cancelled" :value="$summary['cancelled_orders']" icon="ban" accent="navy" />
        <x-stat-card label="Total Income" :value="'$' . number_format($summary['total_income'], 2)" icon="dollar-sign" accent="gold" />
        <x-stat-card label="Confirmed Pipeline Value" :value="'$' . number_format($summary['pipeline_value'], 2)" icon="trending-up" accent="gold" />
    </div>

    <div class="mt-8 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
        <a href="{{ route('admin.media-advocacy.reports.pending') }}" class="card card-body transition hover:shadow-popover">
            <x-icon name="clock" class="h-6 w-6 text-navy-700 dark:text-navy-300" />
            <p class="mt-2 font-semibold text-slate-900 dark:text-white">Pending Requests</p>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Requests waiting on a price and confirmation.</p>
        </a>
        <a href="{{ route('admin.media-advocacy.reports.confirmed') }}" class="card card-body transition hover:shadow-popover">
            <x-icon name="badge-check" class="h-6 w-6 text-navy-700 dark:text-navy-300" />
            <p class="mt-2 font-semibold text-slate-900 dark:text-white">Confirmed &amp; Completed</p>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Priced orders, in progress or finished.</p>
        </a>
        <a href="{{ route('admin.media-advocacy.reports.cancelled') }}" class="card card-body transition hover:shadow-popover">
            <x-icon name="ban" class="h-6 w-6 text-navy-700 dark:text-navy-300" />
            <p class="mt-2 font-semibold text-slate-900 dark:text-white">Cancelled Orders</p>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Requests that didn't move forward.</p>
        </a>
        <a href="{{ route('admin.media-advocacy.reports.income-by-service') }}" class="card card-body transition hover:shadow-popover">
            <x-icon name="tag" class="h-6 w-6 text-navy-700 dark:text-navy-300" />
            <p class="mt-2 font-semibold text-slate-900 dark:text-white">Income by Service</p>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Completed income grouped by category.</p>
        </a>
        <a href="{{ route('admin.media-advocacy.reports.income-by-customer') }}" class="card card-body transition hover:shadow-popover">
            <x-icon name="users" class="h-6 w-6 text-navy-700 dark:text-navy-300" />
            <p class="mt-2 font-semibold text-slate-900 dark:text-white">Income by Customer</p>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Completed income grouped by customer.</p>
        </a>
    </div>
</x-layouts::admin>
