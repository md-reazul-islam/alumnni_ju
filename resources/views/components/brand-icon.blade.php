@props(['boxClass' => 'bg-navy-800 text-gold-400', 'boxSize' => 'h-8 w-8', 'iconSize' => 'h-4 w-4', 'logoHeight' => 'h-8'])

@php
    $logo = \App\Models\Setting::get('general', 'logo');
    $icon = \App\Models\Setting::get('general', 'icon');
@endphp

@if ($logo)
    <img src="{{ asset('storage/' . $logo) }}" alt="{{ config('app.name') }}" class="{{ $logoHeight }} w-auto flex-shrink-0 object-contain">
@else
    <span class="flex {{ $boxSize }} flex-shrink-0 items-center justify-center rounded-lg {{ $boxClass }}">
        @if ($icon)
            <img src="{{ asset('storage/' . $icon) }}" alt="" class="{{ $iconSize }} object-contain">
        @else
            <x-icon name="graduation-cap" class="{{ $iconSize }}" />
        @endif
    </span>
@endif
