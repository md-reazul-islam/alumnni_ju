<x-layouts::admin :title="'Matrimony Reports'">
    <x-breadcrumb :items="[['label' => 'Matrimony'], ['label' => 'Reports']]" class="mb-4" />
    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Matrimony Reports</h1>

    <div class="mt-6 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
        <x-stat-card label="Pending Profiles" :value="$summary['pending_profiles']" icon="clock" accent="gold" />
        <x-stat-card label="Approved Profiles" :value="$summary['approved_profiles']" icon="badge-check" accent="emerald" />
        <x-stat-card label="Rejected Profiles" :value="$summary['rejected_profiles']" icon="circle-x" accent="navy" />
        <x-stat-card label="Suspended Profiles" :value="$summary['suspended_profiles']" icon="ban" accent="navy" />
        <x-stat-card label="Verified Profiles" :value="$summary['verified_profiles']" icon="badge-check" accent="emerald" />
        <x-stat-card label="Male Profiles" :value="$summary['male_profiles']" icon="user" accent="sky" />
        <x-stat-card label="Female Profiles" :value="$summary['female_profiles']" icon="user" accent="sky" />
        <x-stat-card label="Total Interests Sent" :value="$summary['total_interests']" icon="heart" accent="gold" />
        <x-stat-card label="Accepted Interests" :value="$summary['accepted_interests']" icon="circle-check-big" accent="emerald" />
        <x-stat-card label="Declined Interests" :value="$summary['declined_interests']" icon="circle-x" accent="navy" />
        <x-stat-card label="Open Complaints" :value="$summary['open_complaints']" icon="triangle-alert" accent="navy" />
    </div>

    <div class="mt-8 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
        <a href="{{ route('admin.matrimony.reports.profiles') }}" class="card card-body transition hover:shadow-popover">
            <x-icon name="heart" class="h-6 w-6 text-navy-700 dark:text-navy-300" />
            <p class="mt-2 font-semibold text-slate-900 dark:text-white">All Profiles</p>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Every profile, filterable by status and country.</p>
        </a>
        <a href="{{ route('admin.matrimony.reports.interests') }}" class="card card-body transition hover:shadow-popover">
            <x-icon name="users" class="h-6 w-6 text-navy-700 dark:text-navy-300" />
            <p class="mt-2 font-semibold text-slate-900 dark:text-white">Interest History</p>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Every interest request and its outcome.</p>
        </a>
        <a href="{{ route('admin.matrimony.reports.complaints') }}" class="card card-body transition hover:shadow-popover">
            <x-icon name="triangle-alert" class="h-6 w-6 text-navy-700 dark:text-navy-300" />
            <p class="mt-2 font-semibold text-slate-900 dark:text-white">Complaints</p>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Profile and member complaints filed through the app.</p>
        </a>
        <a href="{{ route('admin.matrimony.conversations.index') }}" class="card card-body transition hover:shadow-popover">
            <x-icon name="message-circle" class="h-6 w-6 text-navy-700 dark:text-navy-300" />
            <p class="mt-2 font-semibold text-slate-900 dark:text-white">Conversations</p>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Oversight of every matrimony conversation.</p>
        </a>
    </div>
</x-layouts::admin>
