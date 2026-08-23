<x-layouts::admin :title="'Pending Trips'">
    <x-breadcrumb :items="[['label' => 'Carpooling'], ['label' => 'Pending Trips']]" class="mb-4" />
    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Pending Trip Schedules</h1>

    @if (session('status'))
        <x-alert variant="success" class="mt-4">{{ session('status') }}</x-alert>
    @endif

    @if ($schedules->isEmpty())
        <x-empty-state icon="calendar" title="No trips awaiting review" class="mt-8" />
    @else
        <div class="mt-6 space-y-4">
            @foreach ($schedules as $schedule)
                <div class="card card-body">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="font-semibold text-slate-900 dark:text-white">{{ $schedule->origin }} &rarr; {{ $schedule->destination }}</p>
                            <p class="text-sm text-slate-500 dark:text-slate-400">
                                {{ $schedule->departure_date->format('M j, Y') }} at {{ \Illuminate\Support\Carbon::parse($schedule->departure_time)->format('g:i A') }}
                                &middot; ${{ number_format($schedule->price_per_seat, 2) }}/seat &middot; {{ $schedule->seats_offered }} seats
                            </p>
                            <p class="mt-1 text-xs text-slate-400">Driver: {{ $schedule->driverProfile->user->full_name }} &middot; {{ $schedule->car->display_name }}</p>
                        </div>
                        <a href="{{ route('admin.carpooling.schedules.show', $schedule) }}" class="btn-primary btn-sm flex-shrink-0">Review</a>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="mt-6">{{ $schedules->links() }}</div>
    @endif
</x-layouts::admin>
