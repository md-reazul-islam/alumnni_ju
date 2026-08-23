<x-layouts::alumni :title="'Seat Requests'">
    <x-breadcrumb :items="[['label' => 'Driver Dashboard', 'url' => route('carpooling.driver.dashboard')], ['label' => 'Seat Requests']]" class="mb-4" />
    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Seat Requests</h1>

    @if (session('status'))
        <x-alert variant="success" class="mt-4">{{ session('status') }}</x-alert>
    @endif

    @if ($bookings->isEmpty())
        <x-empty-state icon="calendar" title="No seat requests yet" class="mt-8" />
    @else
        <div class="mt-6 space-y-4">
            @foreach ($bookings as $booking)
                <div class="card card-body flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <p class="font-semibold text-slate-900 dark:text-white">{{ $booking->passenger->full_name }} &middot; {{ $booking->seats }} seat(s)</p>
                        <p class="text-sm text-slate-500 dark:text-slate-400">
                            {{ $booking->schedule->origin }} &rarr; {{ $booking->schedule->destination }}
                            &middot; {{ $booking->schedule->departure_date->format('M j, Y') }} at {{ \Illuminate\Support\Carbon::parse($booking->schedule->departure_time)->format('g:i A') }}
                        </p>
                        <p class="text-xs text-slate-400">Total fare: ${{ number_format($booking->total_fare, 2) }}</p>
                    </div>

                    <div class="flex items-center gap-2">
                        <x-badge :variant="match($booking->status) { 'accepted', 'confirmed', 'completed' => 'success', 'declined', 'cancelled', 'expired' => 'danger', default => 'warning' }">
                            {{ ucfirst($booking->status) }}
                        </x-badge>

                        @if ($booking->status === 'requested')
                            <form method="POST" action="{{ route('carpooling.driver.bookings.accept', $booking) }}">
                                @csrf
                                <button type="submit" class="btn-primary btn-sm">Accept</button>
                            </form>
                            <form method="POST" action="{{ route('carpooling.driver.bookings.decline', $booking) }}">
                                @csrf
                                <button type="submit" class="btn-secondary btn-sm">Decline</button>
                            </form>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
        <div class="mt-6">{{ $bookings->links() }}</div>
    @endif
</x-layouts::alumni>
