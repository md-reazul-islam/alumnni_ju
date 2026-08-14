@props(['size' => 'md'])

@php
    $sizes = ['sm' => 'h-4 w-4', 'md' => 'h-6 w-6', 'lg' => 'h-9 w-9'];
@endphp

<svg {{ $attributes->class(['animate-spin text-current', $sizes[$size] ?? $sizes['md']]) }} viewBox="0 0 24 24" fill="none">
    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
</svg>
