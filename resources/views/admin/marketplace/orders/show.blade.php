<x-layouts::admin :title="'Order #' . $order->id">
    <x-breadcrumb :items="[['label' => 'Marketplace'], ['label' => 'Orders', 'url' => route('admin.marketplace.orders.index')], ['label' => '#' . $order->id]]" class="mb-4" />

    @if (session('status'))
        <x-alert variant="success" class="mb-4">{{ session('status') }}</x-alert>
    @endif

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            <div class="card card-body">
                <div class="flex items-center justify-between">
                    <h1 class="text-xl font-bold text-slate-900 dark:text-white">Order #{{ $order->id }}</h1>
                    <x-badge :variant="match($order->status) { 'completed' => 'success', 'cancelled' => 'danger', 'ongoing' => 'info', default => 'warning' }">
                        {{ ucfirst($order->status) }}
                    </x-badge>
                </div>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                    <a href="{{ route('admin.marketplace.listings.show', $order->listing) }}" class="hover:underline">{{ $order->listing->title }}</a>
                    &middot; ${{ number_format($order->listing->price, 2) }}
                </p>

                <dl class="mt-4 grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <dt class="text-slate-400">Buyer</dt>
                        <dd class="font-medium text-slate-700 dark:text-slate-200">{{ $order->buyer->full_name }} ({{ $order->buyer->email }})</dd>
                    </div>
                    <div>
                        <dt class="text-slate-400">Seller</dt>
                        <dd class="font-medium text-slate-700 dark:text-slate-200">{{ $order->seller->full_name }} ({{ $order->seller->email }})</dd>
                    </div>
                    @if ($order->handler)
                        <div>
                            <dt class="text-slate-400">Handled By</dt>
                            <dd class="font-medium text-slate-700 dark:text-slate-200">{{ $order->handler->full_name }}</dd>
                        </div>
                    @endif
                    @if ($order->commission_amount)
                        <div>
                            <dt class="text-slate-400">Commission</dt>
                            <dd class="font-medium text-slate-700 dark:text-slate-200">${{ number_format($order->commission_amount, 2) }} ({{ rtrim(rtrim(number_format($order->commission_percentage_snapshot, 2), '0'), '.') }}%)</dd>
                        </div>
                    @endif
                </dl>

                @if ($order->admin_notes)
                    <p class="mt-4 rounded-lg bg-slate-50 p-3 text-sm text-slate-600 dark:bg-navy-900 dark:text-slate-300">{{ $order->admin_notes }}</p>
                @endif
            </div>

            <div class="card card-body">
                <h2 class="font-semibold text-slate-900 dark:text-white">Update Status</h2>
                <form method="POST" action="{{ route('admin.marketplace.orders.status', $order) }}" class="mt-4 space-y-4">
                    @csrf
                    <x-select label="Status" name="status" :selected="$order->status" :options="['pending' => 'Pending', 'ongoing' => 'Ongoing', 'completed' => 'Completed', 'cancelled' => 'Cancelled']" required :placeholder="null" />
                    <x-input label="Final Price (used for commission on completion)" name="final_price" type="number" step="0.01" min="0" :value="$order->final_price ?? $order->listing->price" />
                    <x-textarea label="Admin Notes" name="admin_notes" rows="3">{{ $order->admin_notes }}</x-textarea>
                    <div class="flex justify-end"><x-button type="submit" size="sm">Save</x-button></div>
                </form>
            </div>
        </div>

        <div class="space-y-4">
            <div class="card card-body">
                <h2 class="font-semibold text-slate-900 dark:text-white">Communication</h2>
                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Buyers and sellers never message each other directly — you relay between them.</p>

                <form method="POST" action="{{ route('admin.marketplace.orders.converse', [$order, 'buyer']) }}" class="mt-3">
                    @csrf
                    <x-button type="submit" size="sm" class="w-full">{{ $order->buyerConversation ? 'View Buyer Thread' : 'Message Buyer' }}</x-button>
                </form>

                <form method="POST" action="{{ route('admin.marketplace.orders.converse', [$order, 'seller']) }}" class="mt-2">
                    @csrf
                    <x-button type="submit" variant="secondary" size="sm" class="w-full">{{ $order->sellerConversation ? 'View Seller Thread' : 'Message Seller' }}</x-button>
                </form>
            </div>
        </div>
    </div>
</x-layouts::admin>
