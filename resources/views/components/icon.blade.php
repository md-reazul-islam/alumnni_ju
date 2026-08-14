@props(['name', 'class' => 'w-5 h-5'])

<x-dynamic-component :component="'icon.' . $name" :class="$class" {{ $attributes }} />
