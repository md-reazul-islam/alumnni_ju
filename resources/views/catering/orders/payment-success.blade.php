<x-layouts::alumni :title="'Payment Received'">
    <div class="card card-body mx-auto mt-10 max-w-md text-center">
        <x-icon name="circle-check" class="mx-auto h-12 w-12 text-emerald-500" />
        <h1 class="mt-4 text-xl font-bold text-slate-900 dark:text-white">Payment Received</h1>
        <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">
            We're confirming your payment for the "{{ $order->category->name }}" catering order.
            It will show as confirmed on your order page within a moment.
        </p>
        <x-button :href="route('catering.orders.show', $order)" class="mt-6">View Order</x-button>
    </div>
</x-layouts::alumni>
