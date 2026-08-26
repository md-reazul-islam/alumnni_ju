<x-layouts::alumni :title="'My Catering Orders'">
    <x-breadcrumb :items="[['label' => 'My Catering Orders']]" class="mb-4" />
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">My Catering Orders</h1>
        <x-button :href="route('catering.orders.create')" size="sm">New Order</x-button>
    </div>

    @if (session('status'))
        <x-alert variant="success" class="mt-4">{{ session('status') }}</x-alert>
    @endif

    @if ($orders->isEmpty())
        <x-empty-state icon="utensils" title="No catering orders yet" description="Build your first order and we'll price it for you." class="mt-8" />
    @else
        <div class="mt-6 space-y-4">
            @foreach ($orders as $order)
                <a href="{{ route('catering.orders.show', $order) }}" class="card card-body flex flex-wrap items-center justify-between gap-3 transition hover:shadow-popover">
                    <div>
                        <p class="font-semibold text-slate-900 dark:text-white">{{ $order->category->name }}</p>
                        <p class="text-sm text-slate-500 dark:text-slate-400">Event date: {{ $order->event_date->format('M j, Y') }} &middot; Submitted {{ $order->created_at->diffForHumans() }}</p>
                    </div>
                    <div class="flex items-center gap-3">
                        @if ($order->total_amount)
                            <span class="font-semibold text-slate-900 dark:text-white">${{ number_format($order->total_amount, 2) }}</span>
                        @endif
                        <x-badge :variant="match($order->status) { 'accepted', 'delivered' => 'success', 'declined', 'cancelled' => 'danger', default => 'warning' }">
                            {{ ucfirst($order->status) }}
                        </x-badge>
                    </div>
                </a>
            @endforeach
        </div>
        <div class="mt-6">{{ $orders->links() }}</div>
    @endif
</x-layouts::alumni>
