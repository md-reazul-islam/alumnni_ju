@props(['variant' => 'info', 'title' => null, 'dismissible' => false])

@php
    $config = [
        'success' => ['classes' => 'bg-emerald-50 text-emerald-800 ring-emerald-200 dark:bg-emerald-900/30 dark:text-emerald-200', 'icon' => 'circle-check'],
        'warning' => ['classes' => 'bg-amber-50 text-amber-800 ring-amber-200 dark:bg-amber-900/30 dark:text-amber-200', 'icon' => 'triangle-alert'],
        'danger' => ['classes' => 'bg-red-50 text-red-800 ring-red-200 dark:bg-red-900/30 dark:text-red-200', 'icon' => 'circle-alert'],
        'info' => ['classes' => 'bg-sky-50 text-sky-800 ring-sky-200 dark:bg-sky-900/30 dark:text-sky-200', 'icon' => 'info'],
    ];
    $current = $config[$variant] ?? $config['info'];
@endphp

<div
    @if($dismissible) x-data="{ show: true }" x-show="show" x-transition @endif
    {{ $attributes->class(['flex items-start gap-3 rounded-xl p-4 ring-1 ring-inset', $current['classes']]) }}
    role="alert"
>
    <x-icon :name="$current['icon']" class="mt-0.5 h-5 w-5 flex-shrink-0" />

    <div class="flex-1 text-sm">
        @if ($title)
            <p class="font-semibold">{{ $title }}</p>
        @endif
        <div>{{ $slot }}</div>
    </div>

    @if ($dismissible)
        <button type="button" @click="show = false" class="flex-shrink-0 rounded-md p-1 hover:bg-black/5">
            <x-icon name="x" class="h-4 w-4" />
        </button>
    @endif
</div>
