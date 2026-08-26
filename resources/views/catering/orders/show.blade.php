<x-layouts::alumni :title="'Catering Order #' . $order->id">
    <x-breadcrumb :items="[['label' => 'My Catering Orders', 'url' => route('catering.orders.mine')], ['label' => 'Order #' . $order->id]]" class="mb-4" />

    @if (session('status'))
        <x-alert variant="success" class="mt-4">{{ session('status') }}</x-alert>
    @endif

    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <x-badge :variant="match($order->status) { 'accepted', 'delivered' => 'success', 'declined', 'cancelled' => 'danger', default => 'warning' }">
                {{ ucfirst($order->status) }}
            </x-badge>
            <h1 class="mt-2 text-2xl font-bold text-slate-900 dark:text-white">{{ $order->category->name }}</h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Event date: {{ $order->event_date->format('l, M j, Y') }}{{ $order->guest_count ? ' · ' . $order->guest_count . ' guests' : '' }}</p>
        </div>
    </div>

    @if ($order->status === 'submitted')
        <x-alert variant="info" class="mt-4">Your order has been submitted and is awaiting pricing from our catering team. We'll notify you once it's ready to review.</x-alert>
    @endif

    <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2">
            <div class="card card-body">
                <h2 class="font-semibold text-slate-900 dark:text-white">Items</h2>
                <div class="mt-3 divide-y divide-slate-100 dark:divide-navy-800">
                    @foreach ($order->items as $item)
                        <div class="flex items-center justify-between gap-3 py-2 text-sm">
                            <div>
                                <p class="font-medium text-slate-900 dark:text-white">{{ $item->display_name }}</p>
                                <p class="text-xs text-slate-400">Qty: {{ $item->quantity }}@if ($item->isCustom()) &middot; custom item @endif</p>
                            </div>
                            <span class="font-medium text-slate-700 dark:text-slate-200">
                                @if ($item->isPriced())
                                    ${{ number_format($item->line_total, 2) }}
                                @else
                                    <span class="text-amber-600 dark:text-amber-400">Pending price</span>
                                @endif
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>

            @if ($order->notes)
                <div class="card card-body mt-4">
                    <h2 class="font-semibold text-slate-900 dark:text-white">Notes</h2>
                    <p class="mt-2 whitespace-pre-line text-sm text-slate-600 dark:text-slate-300">{{ $order->notes }}</p>
                </div>
            @endif
        </div>

        <div class="space-y-4">
            @if ($order->total_amount)
                <div class="card card-body">
                    <dl class="space-y-1.5 text-sm">
                        <div class="flex justify-between"><dt class="text-slate-400">Subtotal</dt><dd>${{ number_format($order->subtotal, 2) }}</dd></div>
                        <div class="flex justify-between"><dt class="text-slate-400">Tax ({{ rtrim(rtrim(number_format($order->tax_percentage_snapshot, 2), '0'), '.') }}%)</dt><dd>${{ number_format($order->tax_amount, 2) }}</dd></div>
                        <div class="flex justify-between"><dt class="text-slate-400">VAT ({{ rtrim(rtrim(number_format($order->vat_percentage_snapshot, 2), '0'), '.') }}%)</dt><dd>${{ number_format($order->vat_amount, 2) }}</dd></div>
                        <div class="flex justify-between"><dt class="text-slate-400">Service Fee ({{ rtrim(rtrim(number_format($order->service_fee_percentage_snapshot, 2), '0'), '.') }}%)</dt><dd>${{ number_format($order->service_fee_amount, 2) }}</dd></div>
                        <div class="mt-1 flex justify-between border-t border-slate-100 pt-1.5 font-semibold text-slate-900 dark:border-navy-800 dark:text-white"><dt>Total</dt><dd>${{ number_format($order->total_amount, 2) }}</dd></div>
                    </dl>

                    @if ($order->status === 'priced')
                        <div class="mt-4 space-y-2" x-data="{ declining: false }">
                            @if (Route::has('catering.orders.accept'))
                                <x-button :href="route('catering.orders.accept', $order)" class="w-full">Accept &amp; Pay</x-button>
                            @else
                                <x-badge variant="info">Payment opens soon</x-badge>
                            @endif

                            <button type="button" @click="declining = !declining" class="w-full text-sm font-medium text-red-600 hover:underline">Decline this invoice</button>

                            <form method="POST" action="{{ route('catering.orders.decline', $order) }}" x-show="declining" x-cloak class="space-y-2">
                                @csrf
                                <x-textarea label="Reason (optional)" name="rejection_reason" rows="2" />
                                <x-button type="submit" variant="secondary" size="sm" class="w-full">Confirm Decline</x-button>
                            </form>
                        </div>
                    @endif
                </div>
            @endif

            @if ($order->status === 'declined' && $order->rejection_reason)
                <div class="card card-body">
                    <p class="text-sm text-slate-400">Your decline reason</p>
                    <p class="text-sm text-slate-700 dark:text-slate-200">{{ $order->rejection_reason }}</p>
                </div>
            @endif

            <div class="card card-body">
                <p class="text-sm text-slate-400">Delivery Address</p>
                <p class="font-medium text-slate-900 dark:text-white">{{ $order->delivery_address ?: '—' }}</p>
                <p class="mt-3 text-sm text-slate-400">Contact Phone</p>
                <p class="font-medium text-slate-900 dark:text-white">{{ $order->contact_phone ?: '—' }}</p>
            </div>
        </div>
    </div>
</x-layouts::alumni>
