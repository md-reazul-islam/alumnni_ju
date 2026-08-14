@props(['label', 'value', 'icon' => 'chart-bar', 'trend' => null, 'accent' => 'navy'])

@php
    $accents = [
        'navy' => 'bg-navy-50 text-navy-700 dark:bg-navy-800 dark:text-navy-200',
        'gold' => 'bg-gold-50 text-gold-700 dark:bg-gold-900/40 dark:text-gold-300',
        'emerald' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300',
        'sky' => 'bg-sky-50 text-sky-700 dark:bg-sky-900/40 dark:text-sky-300',
    ];
@endphp

<div {{ $attributes->class(['card card-body flex items-start justify-between gap-4']) }}>
    <div class="min-w-0">
        <p class="truncate text-sm font-medium text-slate-500 dark:text-slate-400">{{ $label }}</p>
        <p class="mt-1.5 text-2xl font-bold text-slate-900 dark:text-white">{{ $value }}</p>

        @if ($trend)
            <p class="mt-1.5 flex items-center gap-1 text-xs font-medium {{ str_starts_with($trend, '-') ? 'text-red-600' : 'text-emerald-600' }}">
                <x-icon name="trending-up" class="h-3.5 w-3.5" />
                {{ $trend }}
            </p>
        @endif
    </div>

    <div class="flex h-11 w-11 flex-shrink-0 items-center justify-center rounded-xl {{ $accents[$accent] ?? $accents['navy'] }}">
        <x-icon :name="$icon" class="h-5 w-5" />
    </div>
</div>
