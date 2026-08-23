<x-layouts::admin :title="'Driver Payouts'">
    <x-breadcrumb :items="[['label' => 'Carpooling'], ['label' => 'Payouts']]" class="mb-4" />
    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Driver Payouts</h1>

    @if (session('status'))
        <x-alert variant="success" class="mt-4">{{ session('status') }}</x-alert>
    @endif

    @if ($totalsByDriver->isNotEmpty())
        <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($totalsByDriver as $row)
                <div class="card card-body">
                    <p class="font-semibold text-slate-900 dark:text-white">{{ $row->first_name }} {{ $row->last_name }}</p>
                    <p class="mt-1 text-2xl font-bold text-emerald-600 dark:text-emerald-400">${{ number_format($row->total_due, 2) }}</p>
                    <p class="text-xs text-slate-400">{{ $row->trip_count }} unpaid trip(s)</p>
                </div>
            @endforeach
        </div>
    @endif

    @if ($bookings->isEmpty())
        <x-empty-state icon="dollar-sign" title="No pending payouts" description="Confirmed, paid trips waiting to be paid out to drivers will appear here." class="mt-8" />
    @else
        <x-table class="mt-6">
            <thead><tr><th>Driver</th><th>Passenger</th><th>Trip</th><th>Fare</th><th>Commission</th><th>Payout Owed</th><th></th></tr></thead>
            <tbody>
                @foreach ($bookings as $booking)
                    <tr>
                        <td class="font-medium text-slate-900 dark:text-white">{{ $booking->schedule->driverProfile->user->full_name }}</td>
                        <td>{{ $booking->passenger->full_name }}</td>
                        <td>{{ $booking->schedule->origin }} &rarr; {{ $booking->schedule->destination }}<br><span class="text-xs text-slate-400">{{ $booking->schedule->departure_date->format('M j, Y') }}</span></td>
                        <td>${{ number_format($booking->total_fare, 2) }}</td>
                        <td>${{ number_format($booking->commission_amount, 2) }} ({{ rtrim(rtrim(number_format($booking->commission_percentage_snapshot, 2), '0'), '.') }}%)</td>
                        <td class="font-semibold text-slate-900 dark:text-white">${{ number_format($booking->driver_payout_amount, 2) }}</td>
                        <td>
                            <form method="POST" action="{{ route('admin.carpooling.payouts.mark-paid', $booking) }}" onsubmit="return confirm('Confirm you have paid this driver ${{ number_format($booking->driver_payout_amount, 2) }} outside the app?')">
                                @csrf
                                <button type="submit" class="btn-primary btn-sm">Mark Paid</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </x-table>
        <div class="mt-6">{{ $bookings->links() }}</div>
    @endif
</x-layouts::admin>
