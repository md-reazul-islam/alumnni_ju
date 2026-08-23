<x-layouts::alumni :title="'Payment Cancelled'">
    <div class="card card-body mx-auto mt-10 max-w-md text-center">
        <x-icon name="circle-alert" class="mx-auto h-12 w-12 text-amber-500" />
        <h1 class="mt-4 text-xl font-bold text-slate-900 dark:text-white">Payment Cancelled</h1>
        <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">
            No charge was made for {{ $booking->schedule->origin }} &rarr; {{ $booking->schedule->destination }}.
            You can try paying again before the payment window closes.
        </p>
        <x-button :href="route('carpooling.bookings.index')" class="mt-6">Back to My Ride Requests</x-button>
    </div>
</x-layouts::alumni>
