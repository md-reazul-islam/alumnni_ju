<x-layouts::alumni :title="'Driver Dashboard'">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Driver Dashboard</h1>
        <div class="flex gap-2">
            <x-button :href="route('carpooling.cars.index')" variant="secondary" size="sm">My Cars</x-button>
            @if (Route::has('carpooling.schedules.create'))
                <x-button :href="route('carpooling.schedules.create')" size="sm">Post a Trip</x-button>
            @endif
        </div>
    </div>

    @if (session('status'))
        <x-alert variant="success" class="mt-4">{{ session('status') }}</x-alert>
    @endif

    <div class="mt-6 grid grid-cols-2 gap-4 sm:grid-cols-4">
        <x-stat-card label="Cars" :value="$profile->cars->count()" icon="car" accent="navy" />
        <x-stat-card label="Trips Posted" :value="$profile->schedules->count()" icon="calendar" accent="sky" />
        <x-stat-card label="Pending Requests" :value="$pendingRequestsCount" icon="clock" accent="gold" />
        <x-stat-card label="Total Earned" :value="'$' . number_format($profile->total_earned, 2)" icon="dollar-sign" accent="emerald" />
    </div>

    <div class="mt-8 card card-body">
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-semibold text-slate-900 dark:text-white">My Trips</h2>
            @if (Route::has('carpooling.driver.bookings.index'))
                <a href="{{ route('carpooling.driver.bookings.index') }}" class="text-sm font-medium text-navy-700 hover:underline dark:text-navy-300">View seat requests</a>
            @endif
        </div>

        @if ($profile->schedules->isEmpty())
            <x-empty-state icon="calendar" title="No trips posted yet" class="mt-4" />
        @else
            <div class="mt-4 space-y-3">
                @foreach ($profile->schedules as $schedule)
                    <div class="flex flex-wrap items-center justify-between gap-3 rounded-lg border border-slate-100 p-3 dark:border-navy-800">
                        <div>
                            <p class="font-medium text-slate-900 dark:text-white">{{ $schedule->origin }} &rarr; {{ $schedule->destination }}</p>
                            <p class="text-sm text-slate-500 dark:text-slate-400">{{ $schedule->departure_date->format('M j, Y') }} at {{ \Illuminate\Support\Carbon::parse($schedule->departure_time)->format('g:i A') }}</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <x-badge :variant="match($schedule->status) { 'approved' => 'success', 'rejected' => 'danger', 'cancelled' => 'neutral', 'completed' => 'info', default => 'warning' }">
                                {{ ucfirst($schedule->status) }}
                            </x-badge>
                            <span class="text-sm text-slate-500 dark:text-slate-400">{{ $schedule->seats_booked }}/{{ $schedule->seats_offered }} booked</span>
                            @if (Route::has('carpooling.schedules.edit'))
                                <a href="{{ route('carpooling.schedules.edit', $schedule) }}" class="text-sm font-medium text-navy-700 hover:underline dark:text-navy-300">Edit</a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</x-layouts::alumni>
