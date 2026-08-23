<x-layouts::admin :title="'Payment History'">
    <x-breadcrumb :items="[['label' => 'Carpooling'], ['label' => 'Reports', 'url' => route('admin.carpooling.reports.index')], ['label' => 'Payment History']]" class="mb-4" />
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Payment History</h1>
        <a href="{{ route('admin.carpooling.reports.export', 'payments') }}" class="btn-secondary btn-sm">Export CSV</a>
    </div>

    <form method="GET" class="mt-4 flex gap-3">
        <x-select label="" name="status" placeholder="All statuses" :options="['pending' => 'Pending', 'succeeded' => 'Succeeded', 'failed' => 'Failed', 'refunded' => 'Refunded']" :selected="request('status')" />
        <div class="flex items-end"><x-button type="submit" variant="secondary" size="sm">Filter</x-button></div>
    </form>

    @if ($payments->isEmpty())
        <x-empty-state icon="dollar-sign" title="No payments yet" class="mt-8" />
    @else
        <x-table class="mt-6">
            <thead><tr><th>Booking</th><th>Passenger</th><th>Driver</th><th>Amount</th><th>Status</th><th>Paid At</th></tr></thead>
            <tbody>
                @foreach ($payments as $payment)
                    <tr>
                        <td>#{{ $payment->carpool_booking_id }}</td>
                        <td>{{ $payment->booking?->passenger?->full_name }}</td>
                        <td>{{ $payment->booking?->schedule?->driverProfile?->user?->full_name }}</td>
                        <td>${{ number_format($payment->amount, 2) }}</td>
                        <td><x-badge :variant="match($payment->status) { 'succeeded' => 'success', 'failed', 'refunded' => 'danger', default => 'warning' }">{{ ucfirst($payment->status) }}</x-badge></td>
                        <td>{{ $payment->paid_at?->format('M j, Y g:i A') ?? '—' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </x-table>
        <div class="mt-6">{{ $payments->links() }}</div>
    @endif
</x-layouts::admin>
