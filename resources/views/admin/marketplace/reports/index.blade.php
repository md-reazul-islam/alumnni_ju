<x-layouts::admin :title="'Marketplace Reports'">
    <x-breadcrumb :items="[['label' => 'Marketplace'], ['label' => 'Reports']]" class="mb-4" />
    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Marketplace Reports</h1>

    <div class="mt-6 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
        <x-stat-card label="Total Listings" :value="$summary['total_listings']" icon="shopping-bag" accent="navy" />
        <x-stat-card label="Pending Review" :value="$summary['pending_listings']" icon="clock" accent="gold" />
        <x-stat-card label="Approved Listings" :value="$summary['approved_listings']" icon="badge-check" accent="emerald" />
        <x-stat-card label="Total Orders" :value="$summary['total_orders']" icon="clipboard-list" accent="sky" />
        <x-stat-card label="Ongoing Orders" :value="$summary['ongoing_orders']" icon="loader-circle" accent="sky" />
        <x-stat-card label="Completed Orders" :value="$summary['completed_orders']" icon="circle-check-big" accent="emerald" />
        <x-stat-card label="Total Commission" :value="'$' . number_format($summary['total_commission'], 2)" icon="dollar-sign" accent="gold" />
    </div>

    <div class="mt-8 grid grid-cols-1 gap-5 sm:grid-cols-3">
        <a href="{{ route('admin.marketplace.reports.orders') }}" class="card card-body transition hover:shadow-popover">
            <x-icon name="clipboard-list" class="h-6 w-6 text-navy-700 dark:text-navy-300" />
            <p class="mt-2 font-semibold text-slate-900 dark:text-white">Order Report</p>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Every order, filterable by delivery status.</p>
        </a>
        <a href="{{ route('admin.marketplace.reports.sellers') }}" class="card card-body transition hover:shadow-popover">
            <x-icon name="users" class="h-6 w-6 text-navy-700 dark:text-navy-300" />
            <p class="mt-2 font-semibold text-slate-900 dark:text-white">Seller Report</p>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Listings, completed deals, and commission per seller.</p>
        </a>
        <a href="{{ route('admin.marketplace.reports.categories') }}" class="card card-body transition hover:shadow-popover">
            <x-icon name="tags" class="h-6 w-6 text-navy-700 dark:text-navy-300" />
            <p class="mt-2 font-semibold text-slate-900 dark:text-white">Category Report</p>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Listings and commission earned per category.</p>
        </a>
    </div>
</x-layouts::admin>
