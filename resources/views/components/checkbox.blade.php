@props(['label' => null, 'name', 'value' => '1', 'checked' => false, 'id' => null])

@php $inputId = $id ?? $name . '-' . \Illuminate\Support\Str::slug($value); @endphp

<label for="{{ $inputId }}" class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300">
    <input
        type="checkbox"
        id="{{ $inputId }}"
        name="{{ $name }}"
        value="{{ $value }}"
        @checked($checked)
        {{ $attributes->class(['rounded border-slate-300 text-navy-700 focus:ring-navy-500']) }}
    >
    {{ $label ?? $slot }}
</label>
