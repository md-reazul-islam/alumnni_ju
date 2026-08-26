@php
    $itemsForJs = $order->items->map(fn ($item) => [
        'id' => $item->id,
        'name' => $item->display_name,
        'quantity' => $item->quantity,
        'unit_price' => $item->unit_price ? (float) $item->unit_price : null,
    ])->values();
@endphp

<x-layouts::admin :title="'Catering Order #' . $order->id">
    <x-breadcrumb :items="[['label' => 'Catering'], ['label' => 'Orders', 'url' => route('admin.catering.orders.index')], ['label' => '#' . $order->id]]" class="mb-4" />

    <div x-data="{ rejecting: false }">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <x-badge :variant="match($order->status) { 'accepted', 'delivered' => 'success', 'declined', 'cancelled' => 'danger', default => 'warning' }">
                    {{ ucfirst($order->status) }}
                </x-badge>
                <h1 class="mt-2 text-2xl font-bold text-slate-900 dark:text-white">Order #{{ $order->id }} &middot; {{ $order->category->name }}</h1>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                    {{ $order->customer->full_name }} ({{ $order->customer->email }}) &middot; Event {{ $order->event_date->format('M j, Y') }}{{ $order->guest_count ? ' · ' . $order->guest_count . ' guests' : '' }}
                </p>
            </div>

            @if ($order->status === 'submitted')
                <button type="button" @click="rejecting = !rejecting" class="btn-secondary btn-sm">Reject Order</button>
            @endif

            @if ($order->status === 'accepted')
                <div class="flex flex-shrink-0 gap-2">
                    <form method="POST" action="{{ route('admin.catering.orders.deliver', $order) }}">
                        @csrf
                        <button type="submit" class="btn-primary btn-sm">Mark Delivered</button>
                    </form>
                    <form method="POST" action="{{ route('admin.catering.orders.cancel', $order) }}" onsubmit="event.preventDefault(); Swal.fire({title:'Cancel this order?',text:'The customer will be fully refunded.',input:'text',inputLabel:'Reason',inputPlaceholder:'Why are you cancelling?',showCancelButton:true,confirmButtonColor:'#dc2626',confirmButtonText:'Cancel & Refund'}).then(r=>{ if(r.isConfirmed && r.value){ this.querySelector('[name=cancellation_reason]').value = r.value; this.submit(); } })">
                        @csrf
                        <input type="hidden" name="cancellation_reason" value="">
                        <button type="submit" class="btn-secondary btn-sm">Cancel &amp; Refund</button>
                    </form>
                </div>
            @endif
        </div>

        <form method="POST" action="{{ route('admin.catering.orders.reject', $order) }}" x-show="rejecting" x-cloak class="card card-body mt-4 space-y-3">
            @csrf
            <x-textarea label="Reason (sent to the customer)" name="cancellation_reason" rows="3" required />
            <div class="flex justify-end"><x-button type="submit" variant="danger" size="sm">Confirm Rejection</x-button></div>
        </form>

        @if ($order->status === 'cancelled' && $order->cancellation_reason)
            <x-alert variant="danger" class="mt-4">{{ $order->cancellation_reason }}</x-alert>
        @endif

        @if ($order->status === 'declined' && $order->rejection_reason)
            <x-alert variant="warning" class="mt-4">Customer declined: {{ $order->rejection_reason }}</x-alert>
        @endif

        <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-3">
            <div class="lg:col-span-2 space-y-6">
                @if ($order->status === 'submitted')
                    <form
                        method="POST"
                        action="{{ route('admin.catering.orders.price', $order) }}"
                        x-data="{
                            items: {{ \Illuminate\Support\Js::from($itemsForJs) }},
                            prices: Object.fromEntries({{ \Illuminate\Support\Js::from($itemsForJs) }}.map(i => [i.id, i.unit_price ?? ''])),
                            taxPct: {{ $rates['tax_percentage'] }},
                            vatPct: {{ $rates['vat_percentage'] }},
                            serviceFeePct: {{ $rates['service_fee_percentage'] }},
                            get subtotal() { return this.items.reduce((sum, item) => sum + (parseFloat(this.prices[item.id]) || 0) * item.quantity, 0); },
                            get taxAmount() { return this.subtotal * this.taxPct / 100; },
                            get vatAmount() { return this.subtotal * this.vatPct / 100; },
                            get serviceFeeAmount() { return this.subtotal * this.serviceFeePct / 100; },
                            get total() { return this.subtotal + this.taxAmount + this.vatAmount + this.serviceFeeAmount; },
                        }"
                        class="card card-body"
                    >
                        @csrf
                        <h2 class="font-semibold text-slate-900 dark:text-white">Price This Order</h2>
                        <div class="mt-3 space-y-3">
                            <template x-for="item in items" :key="item.id">
                                <div class="flex items-center justify-between gap-3 rounded-lg border border-slate-100 p-3 dark:border-navy-800">
                                    <div>
                                        <p class="text-sm font-medium text-slate-900 dark:text-white" x-text="item.name"></p>
                                        <p class="text-xs text-slate-400">Qty: <span x-text="item.quantity"></span></p>
                                    </div>
                                    <div class="flex items-center gap-1.5">
                                        <span class="text-sm text-slate-400">$</span>
                                        <input type="number" step="0.01" min="0.01" :name="'prices[' + item.id + ']'" x-model="prices[item.id]" required class="form-input w-28">
                                    </div>
                                </div>
                            </template>
                        </div>

                        <dl class="mt-4 space-y-1.5 border-t border-slate-100 pt-3 text-sm dark:border-navy-800">
                            <div class="flex justify-between"><dt class="text-slate-400">Subtotal</dt><dd x-text="'$' + subtotal.toFixed(2)"></dd></div>
                            <div class="flex justify-between"><dt class="text-slate-400">Tax (<span x-text="taxPct"></span>%)</dt><dd x-text="'$' + taxAmount.toFixed(2)"></dd></div>
                            <div class="flex justify-between"><dt class="text-slate-400">VAT (<span x-text="vatPct"></span>%)</dt><dd x-text="'$' + vatAmount.toFixed(2)"></dd></div>
                            <div class="flex justify-between"><dt class="text-slate-400">Service Fee (<span x-text="serviceFeePct"></span>%)</dt><dd x-text="'$' + serviceFeeAmount.toFixed(2)"></dd></div>
                            <div class="flex justify-between border-t border-slate-100 pt-1.5 font-semibold text-slate-900 dark:border-navy-800 dark:text-white"><dt>Total</dt><dd x-text="'$' + total.toFixed(2)"></dd></div>
                        </dl>

                        <x-textarea label="Internal notes (optional)" name="admin_notes" rows="2" class="mt-4" />

                        <div class="mt-4 flex justify-end"><x-button type="submit">Price &amp; Send Invoice</x-button></div>
                    </form>
                @else
                    <div class="card card-body">
                        <h2 class="font-semibold text-slate-900 dark:text-white">Items</h2>
                        <div class="mt-3 divide-y divide-slate-100 dark:divide-navy-800">
                            @foreach ($order->items as $item)
                                <div class="flex items-center justify-between gap-3 py-2 text-sm">
                                    <div>
                                        <p class="font-medium text-slate-900 dark:text-white">{{ $item->display_name }}</p>
                                        <p class="text-xs text-slate-400">Qty: {{ $item->quantity }} &middot; ${{ number_format($item->unit_price, 2) }} each</p>
                                    </div>
                                    <span class="font-medium text-slate-700 dark:text-slate-200">${{ number_format($item->line_total, 2) }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if ($order->notes)
                    <div class="card card-body">
                        <h2 class="font-semibold text-slate-900 dark:text-white">Customer Notes</h2>
                        <p class="mt-2 whitespace-pre-line text-sm text-slate-600 dark:text-slate-300">{{ $order->notes }}</p>
                    </div>
                @endif
            </div>

            <div class="space-y-4">
                @if ($order->total_amount)
                    <div class="card card-body">
                        <p class="text-sm text-slate-400">Total</p>
                        <p class="text-2xl font-bold text-slate-900 dark:text-white">${{ number_format($order->total_amount, 2) }}</p>
                        <p class="mt-1 text-xs text-slate-400">Priced by {{ $order->pricer?->full_name }} on {{ $order->priced_at?->format('M j, Y') }}</p>
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
    </div>
</x-layouts::admin>
