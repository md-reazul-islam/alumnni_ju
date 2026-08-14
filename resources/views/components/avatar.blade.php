@props(['src' => null, 'name' => '', 'size' => 'md'])

@php
    $sizes = [
        'xs' => 'h-6 w-6 text-[10px]',
        'sm' => 'h-8 w-8 text-xs',
        'md' => 'h-10 w-10 text-sm',
        'lg' => 'h-14 w-14 text-base',
        'xl' => 'h-24 w-24 text-2xl',
    ];
    $sizeClass = $sizes[$size] ?? $sizes['md'];
    $url = $src ?: 'https://ui-avatars.com/api/?name=' . urlencode($name) . '&background=1c2f56&color=fff';
@endphp

<img
    src="{{ $url }}"
    alt="{{ $name }}"
    {{ $attributes->class(['inline-block flex-shrink-0 rounded-full object-cover ring-2 ring-white dark:ring-navy-900', $sizeClass]) }}
/>
