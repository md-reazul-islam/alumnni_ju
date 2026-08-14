@props(['variant' => 'neutral'])

@php
    $variants = [
        'success' => 'badge-success',
        'warning' => 'badge-warning',
        'danger' => 'badge-danger',
        'info' => 'badge-info',
        'neutral' => 'badge-neutral',
    ];
@endphp

<span {{ $attributes->class([$variants[$variant] ?? $variants['neutral']]) }}>
    {{ $slot }}
</span>
