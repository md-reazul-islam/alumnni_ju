<x-layouts::admin :title="'Catering Reports'">
    <x-breadcrumb :items="[['label' => 'Catering'], ['label' => 'Reports']]" class="mb-4" />
    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Catering Reports</h1>

    <h2 class="mt-6 text-sm font-semibold uppercase tracking-wide text-slate-400">Event Catering</h2>
    <div class="mt-3 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
        <x-stat-card label="Total Orders" :value="$summary['total_orders']" icon="clipboard-list" accent="sky" />
        <x-stat-card label="Submitted" :value="$summary['submitted_orders']" icon="clock" accent="gold" />
        <x-stat-card label="Priced" :value="$summary['priced_orders']" icon="receipt" accent="gold" />
        <x-stat-card label="Accepted" :value="$summary['accepted_orders']" icon="badge-check" accent="emerald" />
        <x-stat-card label="Declined" :value="$summary['declined_orders']" icon="circle-x" accent="navy" />
        <x-stat-card label="Delivered" :value="$summary['delivered_orders']" icon="circle-check-big" accent="emerald" />
        <x-stat-card label="Cancelled" :value="$summary['cancelled_orders']" icon="ban" accent="navy" />
        <x-stat-card label="Total Revenue" :value="'$' . number_format($summary['total_revenue'], 2)" icon="dollar-sign" accent="gold" />
        <x-stat-card label="Avg Order Value" :value="'$' . number_format($summary['avg_order_value'], 2)" icon="dollar-sign" accent="gold" />
        <x-stat-card label="Avg Rating" :value="number_format($summary['average_rating'], 1) . ' / 5'" icon="star" accent="gold" />
    </div>

    <h2 class="mt-8 text-sm font-semibold uppercase tracking-wide text-slate-400">Home Made Foods</h2>
    <div class="mt-3 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
        <x-stat-card label="Pending Listings" :value="$summary['homemade_pending_listings']" icon="clock" accent="gold" />
        <x-stat-card label="Completed Orders" :value="$summary['homemade_completed_orders']" icon="circle-check-big" accent="emerald" />
        <x-stat-card label="Total Commission" :value="'$' . number_format($summary['homemade_total_commission'], 2)" icon="dollar-sign" accent="gold" />
        <x-stat-card label="Open Complaints" :value="$summary['open_complaints']" icon="triangle-alert" accent="navy" />
    </div>

    <div class="mt-8 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
        <a href="{{ route('admin.catering.reports.daily') }}" class="card card-body transition hover:shadow-popover">
            <x-icon name="calendar" class="h-6 w-6 text-navy-700 dark:text-navy-300" />
            <p class="mt-2 font-semibold text-slate-900 dark:text-white">Daily Report</p>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Orders and revenue per event date.</p>
        </a>
        <a href="{{ route('admin.catering.reports.monthly') }}" class="card card-body transition hover:shadow-popover">
            <x-icon name="calendar" class="h-6 w-6 text-navy-700 dark:text-navy-300" />
            <p class="mt-2 font-semibold text-slate-900 dark:text-white">Monthly Report</p>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Orders and revenue rolled up by month.</p>
        </a>
        <a href="{{ route('admin.catering.reports.delivered') }}" class="card card-body transition hover:shadow-popover">
            <x-icon name="circle-check-big" class="h-6 w-6 text-navy-700 dark:text-navy-300" />
            <p class="mt-2 font-semibold text-slate-900 dark:text-white">Delivered Orders</p>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Every order that reached delivery.</p>
        </a>
        <a href="{{ route('admin.catering.reports.cancelled') }}" class="card card-body transition hover:shadow-popover">
            <x-icon name="ban" class="h-6 w-6 text-navy-700 dark:text-navy-300" />
            <p class="mt-2 font-semibold text-slate-900 dark:text-white">Cancelled Orders</p>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Cancellations with reason and refund status.</p>
        </a>
        <a href="{{ route('admin.catering.reports.top-customers') }}" class="card card-body transition hover:shadow-popover">
            <x-icon name="users" class="h-6 w-6 text-navy-700 dark:text-navy-300" />
            <p class="mt-2 font-semibold text-slate-900 dark:text-white">Top Customers</p>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Ranked by total spend.</p>
        </a>
        <a href="{{ route('admin.catering.reports.feedback') }}" class="card card-body transition hover:shadow-popover">
            <x-icon name="star" class="h-6 w-6 text-navy-700 dark:text-navy-300" />
            <p class="mt-2 font-semibold text-slate-900 dark:text-white">Customer Feedback</p>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Ratings and comments left after delivery.</p>
        </a>
        <a href="{{ route('admin.catering.reports.homemade-orders') }}" class="card card-body transition hover:shadow-popover">
            <x-icon name="cooking-pot" class="h-6 w-6 text-navy-700 dark:text-navy-300" />
            <p class="mt-2 font-semibold text-slate-900 dark:text-white">Home Made Orders</p>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Every Home Made Foods order and its status.</p>
        </a>
        <a href="{{ route('admin.catering.reports.homemade-vendors') }}" class="card card-body transition hover:shadow-popover">
            <x-icon name="badge-check" class="h-6 w-6 text-navy-700 dark:text-navy-300" />
            <p class="mt-2 font-semibold text-slate-900 dark:text-white">Home Made Vendors</p>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Top vendors by completed sales.</p>
        </a>
        <a href="{{ route('admin.catering.reports.homemade-categories') }}" class="card card-body transition hover:shadow-popover">
            <x-icon name="tags" class="h-6 w-6 text-navy-700 dark:text-navy-300" />
            <p class="mt-2 font-semibold text-slate-900 dark:text-white">Home Made Categories</p>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Commission earned per category.</p>
        </a>
        <a href="{{ route('admin.catering.reports.complaints') }}" class="card card-body transition hover:shadow-popover">
            <x-icon name="triangle-alert" class="h-6 w-6 text-navy-700 dark:text-navy-300" />
            <p class="mt-2 font-semibold text-slate-900 dark:text-white">Complaints</p>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Complaints filed against orders or vendors.</p>
        </a>
    </div>
</x-layouts::admin>
