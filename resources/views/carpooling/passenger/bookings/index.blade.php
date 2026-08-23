<x-layouts::alumni :title="'My Ride Requests'">
    <x-breadcrumb :items="[['label' => 'My Ride Requests']]" class="mb-4" />
    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">My Ride Requests</h1>

    @if (session('status'))
        <x-alert variant="success" class="mt-4">{{ session('status') }}</x-alert>
    @endif

    @if ($bookings->isEmpty())
        <x-empty-state icon="car" title="No ride requests yet" description="Find a trip and request a seat to get started." class="mt-8" />
    @else
        <div class="mt-6 space-y-4">
            @foreach ($bookings as $booking)
                <div class="card card-body flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <p class="font-semibold text-slate-900 dark:text-white">{{ $booking->schedule->origin }} &rarr; {{ $booking->schedule->destination }}</p>
                        <p class="text-sm text-slate-500 dark:text-slate-400">
                            {{ $booking->schedule->departure_date->format('M j, Y') }} at {{ \Illuminate\Support\Carbon::parse($booking->schedule->departure_time)->format('g:i A') }}
                            &middot; Driver: {{ $booking->schedule->driverProfile->user->full_name }}
                        </p>
                        <p class="text-xs text-slate-400">{{ $booking->seats }} seat(s) &middot; ${{ number_format($booking->total_fare, 2) }} total</p>
                        @if ($booking->status === 'accepted' && $booking->payment_deadline_at)
                            <p class="mt-1 text-xs font-medium text-amber-600 dark:text-amber-400">Pay by {{ $booking->payment_deadline_at->format('M j, g:i A') }} to confirm your seat.</p>
                        @endif
                    </div>

                    <div class="flex items-center gap-2">
                        <x-badge :variant="match($booking->status) { 'accepted', 'confirmed', 'completed' => 'success', 'declined', 'cancelled', 'expired' => 'danger', default => 'warning' }">
                            {{ ucfirst($booking->status) }}
                        </x-badge>

                        @if (in_array($booking->status, ['requested', 'accepted']))
                            <form method="POST" action="{{ route('carpooling.bookings.cancel', $booking) }}" onsubmit="return confirm('Withdraw this request?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-sm font-medium text-red-600 hover:underline">Withdraw</button>
                            </form>
                        @endif

                        @if ($booking->status === 'confirmed')
                            <form method="POST" action="{{ route('carpooling.bookings.cancel', $booking) }}" onsubmit="return confirm('Cancel this confirmed booking? A refund will be requested if you are still within the cancellation window.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-sm font-medium text-red-600 hover:underline">Cancel &amp; Refund</button>
                            </form>
                        @endif

                        @if ($booking->status === 'accepted' && Route::has('carpooling.bookings.pay'))
                            <x-button :href="route('carpooling.bookings.pay', $booking)" size="sm">Pay Now</x-button>
                        @endif

                        @if (in_array($booking->status, ['confirmed', 'completed']))
                            <form method="POST" action="{{ route('reports.store', ['carpool_trip', $booking->id]) }}" onsubmit="event.preventDefault(); Swal.fire({title:'Report a problem with this trip',input:'text',inputLabel:'What happened?',inputPlaceholder:'Briefly describe the issue',showCancelButton:true,confirmButtonText:'Submit'}).then(r=>{ if(r.isConfirmed && r.value){ this.querySelector('[name=reason]').value = r.value; this.submit(); } })">
                                @csrf
                                <input type="hidden" name="reason" value="">
                                <button type="submit" class="text-sm font-medium text-slate-400 hover:text-red-600">Report</button>
                            </form>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
        <div class="mt-6">{{ $bookings->links() }}</div>
    @endif
</x-layouts::alumni>
