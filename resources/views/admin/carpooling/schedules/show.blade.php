<x-layouts::admin :title="$schedule->origin . ' to ' . $schedule->destination">
    <x-breadcrumb :items="[['label' => 'Carpooling'], ['label' => 'Pending Trips', 'url' => route('admin.carpooling.schedules.pending')], ['label' => $schedule->origin . ' to ' . $schedule->destination]]" class="mb-4" />

    <div x-data="{ rejecting: false }">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <x-badge :variant="match($schedule->status) { 'approved' => 'success', 'rejected' => 'danger', 'cancelled' => 'neutral', 'completed' => 'info', default => 'warning' }">
                    {{ ucfirst($schedule->status) }}
                </x-badge>
                <h1 class="mt-2 text-2xl font-bold text-slate-900 dark:text-white">{{ $schedule->origin }} &rarr; {{ $schedule->destination }}</h1>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                    Driver: {{ $schedule->driverProfile->user->full_name }} ({{ $schedule->driverProfile->user->email }}) &middot; {{ $schedule->car->display_name }}
                </p>
            </div>

            @if ($schedule->status === 'pending')
                <div class="flex flex-shrink-0 gap-2">
                    <form method="POST" action="{{ route('admin.carpooling.schedules.approve', $schedule) }}">
                        @csrf
                        <button type="submit" class="btn-primary btn-sm">Approve</button>
                    </form>
                    <button type="button" @click="rejecting = !rejecting" class="btn-secondary btn-sm">Reject</button>
                </div>
            @endif
        </div>

        <form method="POST" action="{{ route('admin.carpooling.schedules.reject', $schedule) }}" x-show="rejecting" x-cloak class="card card-body mt-4 space-y-3">
            @csrf
            <x-textarea label="Reason for rejection" name="rejection_reason" rows="3" required placeholder="Explain what needs to change so the driver knows why." />
            <div class="flex justify-end"><x-button type="submit" variant="danger" size="sm">Confirm Rejection</x-button></div>
        </form>

        @if ($schedule->status === 'rejected' && $schedule->rejection_reason)
            <x-alert variant="danger" class="mt-4">{{ $schedule->rejection_reason }}</x-alert>
        @endif

        <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-3">
            <div class="space-y-6 lg:col-span-2">
                <div class="card card-body">
                    <h2 class="font-semibold text-slate-900 dark:text-white">Passengers</h2>
                    @if ($schedule->bookings->isEmpty())
                        <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">No booking requests yet.</p>
                    @else
                        <div class="mt-3 space-y-2">
                            @foreach ($schedule->bookings as $booking)
                                <div class="flex items-center justify-between rounded-lg border border-slate-100 p-3 text-sm dark:border-navy-800">
                                    <span>{{ $booking->passenger->full_name }} &middot; {{ $booking->seats }} seat(s)</span>
                                    <div class="flex items-center gap-2">
                                        <span class="font-medium text-slate-900 dark:text-white">${{ number_format($booking->total_fare, 2) }}</span>
                                        <x-badge :variant="match($booking->status) { 'confirmed', 'completed' => 'success', 'declined', 'cancelled', 'expired' => 'danger', default => 'warning' }">
                                            {{ ucfirst($booking->status) }}
                                        </x-badge>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                @if ($schedule->notes)
                    <div class="card card-body">
                        <h2 class="font-semibold text-slate-900 dark:text-white">Driver Notes</h2>
                        <p class="mt-2 whitespace-pre-line text-sm text-slate-600 dark:text-slate-300">{{ $schedule->notes }}</p>
                    </div>
                @endif
            </div>

            <div class="space-y-4">
                <div class="card card-body">
                    <p class="text-sm text-slate-400">Departure</p>
                    <p class="font-medium text-slate-900 dark:text-white">{{ $schedule->departure_date->format('M j, Y') }} at {{ \Illuminate\Support\Carbon::parse($schedule->departure_time)->format('g:i A') }}</p>
                </div>
                <div class="card card-body">
                    <p class="text-sm text-slate-400">Price Per Seat</p>
                    <p class="text-xl font-bold text-slate-900 dark:text-white">${{ number_format($schedule->price_per_seat, 2) }}</p>
                    <p class="text-xs text-slate-400">Total if fully booked: ${{ number_format($schedule->price_per_seat * $schedule->seats_offered, 2) }}</p>
                </div>
                <div class="card card-body">
                    <p class="text-sm text-slate-400">Seats</p>
                    <p class="font-medium text-slate-900 dark:text-white">{{ $schedule->seats_booked }} booked / {{ $schedule->seats_offered }} offered</p>
                </div>
                @if ($schedule->approver)
                    <div class="card card-body">
                        <p class="text-sm text-slate-400">Approved By</p>
                        <p class="font-medium text-slate-900 dark:text-white">{{ $schedule->approver->full_name }}</p>
                        <p class="text-xs text-slate-400">{{ $schedule->approved_at?->format('M j, Y') }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layouts::admin>
