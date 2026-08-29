<x-layouts::admin :title="'Order #' . $order->id">
    <x-breadcrumb :items="[['label' => 'Media Advocacy'], ['label' => 'Orders', 'url' => route('admin.media-advocacy.orders.index')], ['label' => '#' . $order->id]]" class="mb-4" />

    @if (session('status'))
        <x-alert variant="success" class="mb-4">{{ session('status') }}</x-alert>
    @endif

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            <div class="card card-body">
                <div class="flex items-center justify-between">
                    <h1 class="text-xl font-bold text-slate-900 dark:text-white">Order #{{ $order->id }}</h1>
                    <x-badge :variant="match($order->status) { 'completed' => 'success', 'cancelled' => 'danger', 'confirmed' => 'info', default => 'warning' }">
                        {{ ucfirst($order->status) }}
                    </x-badge>
                </div>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ $order->category->name }}</p>

                <dl class="mt-4 grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <dt class="text-slate-400">Customer</dt>
                        <dd class="font-medium text-slate-700 dark:text-slate-200">{{ $order->customer->full_name }} ({{ $order->customer->email }})</dd>
                    </div>
                    @if ($order->handler)
                        <div>
                            <dt class="text-slate-400">Handled By</dt>
                            <dd class="font-medium text-slate-700 dark:text-slate-200">{{ $order->handler->full_name }}</dd>
                        </div>
                    @endif
                    @if ($order->final_price)
                        <div>
                            <dt class="text-slate-400">Price</dt>
                            <dd class="font-medium text-slate-700 dark:text-slate-200">${{ number_format($order->final_price, 2) }}</dd>
                        </div>
                    @endif
                </dl>

                @if ($order->admin_notes)
                    <p class="mt-4 rounded-lg bg-slate-50 p-3 text-sm text-slate-600 dark:bg-navy-900 dark:text-slate-300">{{ $order->admin_notes }}</p>
                @endif
            </div>

            <div class="card card-body">
                <h2 class="font-semibold text-slate-900 dark:text-white">Update Status</h2>
                <form method="POST" action="{{ route('admin.media-advocacy.orders.status', $order) }}" class="mt-4 space-y-4">
                    @csrf
                    <x-select label="Status" name="status" :selected="$order->status" :options="['pending' => 'Pending', 'confirmed' => 'Confirmed', 'completed' => 'Completed', 'cancelled' => 'Cancelled']" required :placeholder="null" />
                    <x-input label="Price (required when confirming)" name="final_price" type="number" step="0.01" min="0" :value="$order->final_price" />
                    <x-textarea label="Admin Notes" name="admin_notes" rows="3">{{ $order->admin_notes }}</x-textarea>
                    <div class="flex justify-end"><x-button type="submit" size="sm">Save</x-button></div>
                </form>
            </div>
        </div>

        <div class="space-y-4">
            <div class="card card-body">
                <h2 class="font-semibold text-slate-900 dark:text-white">Communication</h2>
                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Message the customer to agree on scope and price before confirming.</p>

                <form method="POST" action="{{ route('admin.media-advocacy.orders.converse', $order) }}" class="mt-3">
                    @csrf
                    <x-button type="submit" size="sm" class="w-full">{{ $order->buyerConversation ? 'View Thread' : 'Message Customer' }}</x-button>
                </form>
            </div>
        </div>
    </div>
</x-layouts::admin>
