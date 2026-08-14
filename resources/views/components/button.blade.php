@props(['variant' => 'primary', 'size' => 'md', 'href' => null, 'type' => 'button'])

@php
    $variants = [
        'primary' => 'btn-primary',
        'secondary' => 'btn-secondary',
        'gold' => 'btn-gold',
        'ghost' => 'btn-ghost',
        'danger' => 'btn-danger',
    ];
    $classes = ($variants[$variant] ?? $variants['primary']) . ($size === 'sm' ? ' btn-sm' : '');
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->class([$classes]) }}>{{ $slot }}</a>
@else
    <button type="{{ $type }}" {{ $attributes->class([$classes]) }}>{{ $slot }}</button>
@endif
