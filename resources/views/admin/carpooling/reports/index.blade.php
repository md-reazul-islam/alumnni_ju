<x-layouts::admin :title="'Carpooling Reports'">
    <x-breadcrumb :items="[['label' => 'Carpooling'], ['label' => 'Reports']]" class="mb-4" />
    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Carpooling Reports</h1>

    <div class="mt-6 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
        <x-stat-card label="Verified Drivers" :value="$summary['verified_drivers']" icon="badge-check" accent="emerald" />
        <x-stat-card label="Pending Drivers" :value="$summary['pending_drivers']" icon="clock" accent="gold" />
        <x-stat-card label="Rejected Drivers" :value="$summary['rejected_drivers']" icon="circle-x" accent="navy" />
        <x-stat-card label="Suspended Drivers" :value="$summary['suspended_drivers']" icon="ban" accent="navy" />
        <x-stat-card label="Total Trips" :value="$summary['total_trips']" icon="calendar" accent="sky" />
        <x-stat-card label="Completed Trips" :value="$summary['completed_trips']" icon="circle-check-big" accent="emerald" />
        <x-stat-card label="Cancelled Trips" :value="$summary['cancelled_trips']" icon="ban" accent="navy" />
        <x-stat-card label="Confirmed Bookings" :value="$summary['confirmed_bookings']" icon="clipboard-list" accent="sky" />
        <x-stat-card label="Total Revenue" :value="'$' . number_format($summary['total_revenue'], 2)" icon="dollar-sign" accent="gold" />
        <x-stat-card label="Platform Commission" :value="'$' . number_format($summary['total_commission'], 2)" icon="dollar-sign" accent="gold" />
        <x-stat-card label="Pending Payouts" :value="'$' . number_format($summary['pending_payouts'], 2)" icon="dollar-sign" accent="navy" />
        <x-stat-card label="Open Complaints" :value="$summary['open_complaints']" icon="triangle-alert" accent="navy" />
    </div>

    <div class="mt-8 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
        <a href="{{ route('admin.carpooling.reports.days') }}" class="card card-body transition hover:shadow-popover">
            <x-icon name="calendar" class="h-6 w-6 text-navy-700 dark:text-navy-300" />
            <p class="mt-2 font-semibold text-slate-900 dark:text-white">Day-wise Trips</p>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Trips, seats offered, and seats booked per day.</p>
        </a>
        <a href="{{ route('admin.carpooling.reports.drivers') }}" class="card card-body transition hover:shadow-popover">
            <x-icon name="car" class="h-6 w-6 text-navy-700 dark:text-navy-300" />
            <p class="mt-2 font-semibold text-slate-900 dark:text-white">Driver History</p>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Trips posted, completed, cancelled, and earnings per driver.</p>
        </a>
        <a href="{{ route('admin.carpooling.reports.passengers') }}" class="card card-body transition hover:shadow-popover">
            <x-icon name="users" class="h-6 w-6 text-navy-700 dark:text-navy-300" />
            <p class="mt-2 font-semibold text-slate-900 dark:text-white">Passenger History</p>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Bookings and total spend per passenger.</p>
        </a>
        <a href="{{ route('admin.carpooling.reports.payments') }}" class="card card-body transition hover:shadow-popover">
            <x-icon name="dollar-sign" class="h-6 w-6 text-navy-700 dark:text-navy-300" />
            <p class="mt-2 font-semibold text-slate-900 dark:text-white">Payment History</p>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Every Stripe payment attempt and its outcome.</p>
        </a>
        <a href="{{ route('admin.carpooling.reports.complaints') }}" class="card card-body transition hover:shadow-popover">
            <x-icon name="triangle-alert" class="h-6 w-6 text-navy-700 dark:text-navy-300" />
            <p class="mt-2 font-semibold text-slate-900 dark:text-white">Complaints</p>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Passenger and driver complaints filed through the app.</p>
        </a>
        <a href="{{ route('admin.carpooling.payouts.index') }}" class="card card-body transition hover:shadow-popover">
            <x-icon name="badge-check" class="h-6 w-6 text-navy-700 dark:text-navy-300" />
            <p class="mt-2 font-semibold text-slate-900 dark:text-white">Payouts</p>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Manage driver payouts owed by the platform.</p>
        </a>
    </div>
</x-layouts::admin>
