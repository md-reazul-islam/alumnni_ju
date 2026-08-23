<x-layouts::app>
    <div>
      <div class="section-container py-8">
        <x-breadcrumb :items="[['label' => 'Carpooling']]" onDark class="mb-4" />

        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-white">Find a Ride</h1>
                <p class="mt-1.5 text-navy-200">Search trips posted by fellow alumni drivers and share the fare.</p>
            </div>
            @auth
                @if (Route::has('carpooling.driver.dashboard') && auth()->user()->isApprovedCarpoolDriver())
                    <x-button :href="route('carpooling.driver.dashboard')" variant="secondary" size="sm">Driver Dashboard</x-button>
                @else
                    <x-button :href="route('carpooling.driver.become')" size="sm">Become a Driver</x-button>
                @endif
            @endauth
        </div>

        <form method="GET" action="{{ route('carpooling.search') }}" class="card card-body mt-6 grid grid-cols-1 gap-4 sm:grid-cols-4">
            <x-input label="Date" name="date" type="date" :value="$filters['date'] ?? null" />
            <x-input label="From" name="origin" placeholder="Origin" :value="$filters['origin'] ?? null" />
            <x-input label="To" name="destination" placeholder="Destination" :value="$filters['destination'] ?? null" />
            <div class="flex items-end">
                <x-button type="submit" class="w-full">Search</x-button>
            </div>
        </form>

        @if ($schedules->isEmpty())
            <x-empty-state icon="car" title="No matching trips" description="Try a different date or route, or check back soon — approved trips appear here as soon as drivers post them." class="mt-8" />
        @else
            <div class="mt-6 space-y-4">
                @foreach ($schedules as $schedule)
                    <div class="card card-body flex flex-wrap items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <x-icon name="car" class="h-8 w-8 flex-shrink-0 text-navy-600 dark:text-navy-300" />
                            <div>
                                <p class="font-semibold text-slate-900 dark:text-white">{{ $schedule->origin }} &rarr; {{ $schedule->destination }}</p>
                                <p class="text-sm text-slate-500 dark:text-slate-400">
                                    {{ $schedule->departure_date->format('D, M j, Y') }} at {{ \Illuminate\Support\Carbon::parse($schedule->departure_time)->format('g:i A') }}
                                    &middot; {{ $schedule->car->display_name }}
                                </p>
                                <p class="text-xs text-slate-400">Driver: {{ $schedule->driverProfile->user->full_name }} &middot; {{ $schedule->seatsRemaining() }} seat(s) left</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            <p class="text-lg font-bold text-slate-900 dark:text-white">${{ number_format($schedule->price_per_seat, 2) }}<span class="text-sm font-normal text-slate-400">/seat</span></p>
                            @auth
                                @if ($schedule->driverProfile->user_id === auth()->id())
                                    <x-badge variant="info">Your trip</x-badge>
                                @elseif (Route::has('carpooling.bookings.store'))
                                    <form method="POST" action="{{ route('carpooling.bookings.store', $schedule) }}" class="flex items-center gap-2">
                                        @csrf
                                        <select name="seats" class="form-select w-auto py-1.5 text-sm">
                                            @for ($i = 1; $i <= min(4, $schedule->seatsRemaining()); $i++)
                                                <option value="{{ $i }}">{{ $i }} seat{{ $i > 1 ? 's' : '' }}</option>
                                            @endfor
                                        </select>
                                        <x-button type="submit" size="sm">Request Seat</x-button>
                                    </form>
                                @else
                                    <x-badge variant="info">Booking opens soon</x-badge>
                                @endif
                            @else
                                <x-button :href="route('login')" variant="secondary" size="sm">Login to Book</x-button>
                            @endauth
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="mt-6">{{ $schedules->links() }}</div>
        @endif
      </div>
    </div>
</x-layouts::app>
