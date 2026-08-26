@props(['order'])

@php
    $steps = collect([
        ['label' => 'Submitted', 'at' => $order->created_at, 'done' => true],
        ['label' => 'Priced', 'at' => $order->priced_at, 'done' => (bool) $order->priced_at],
    ]);

    if ($order->status === 'declined') {
        $steps->push(['label' => 'Declined', 'at' => $order->customer_responded_at, 'done' => true, 'negative' => true]);
    } elseif ($order->status === 'cancelled') {
        $steps->push(['label' => 'Cancelled', 'at' => $order->cancelled_at, 'done' => true, 'negative' => true]);
    } else {
        $steps->push(['label' => 'Accepted', 'at' => $order->customer_responded_at, 'done' => (bool) $order->customer_responded_at]);
        $steps->push(['label' => 'Delivered', 'at' => $order->delivered_at, 'done' => (bool) $order->delivered_at]);
    }
@endphp

<div class="card card-body">
    <h2 class="font-semibold text-slate-900 dark:text-white">Order Trace</h2>
    <ol class="mt-4 space-y-4">
        @foreach ($steps as $step)
            <li class="flex gap-3">
                <div class="flex flex-col items-center">
                    <span class="flex h-5 w-5 flex-shrink-0 items-center justify-center rounded-full {{ $step['done'] ? (($step['negative'] ?? false) ? 'bg-red-500' : 'bg-emerald-500') : 'bg-slate-200 dark:bg-navy-700' }}">
                        @if ($step['done'])
                            <svg class="h-3 w-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                        @endif
                    </span>
                    @if (!$loop->last)
                        <span class="mt-1 h-6 w-px {{ $step['done'] ? 'bg-emerald-300 dark:bg-emerald-800' : 'bg-slate-200 dark:bg-navy-700' }}"></span>
                    @endif
                </div>
                <div class="pb-1">
                    <p class="text-sm font-medium {{ $step['done'] ? 'text-slate-900 dark:text-white' : 'text-slate-400' }}">{{ $step['label'] }}</p>
                    @if ($step['at'])
                        <p class="text-xs text-slate-400">{{ $step['at']->format('M j, Y g:i A') }}</p>
                    @endif
                </div>
            </li>
        @endforeach
    </ol>
</div>
