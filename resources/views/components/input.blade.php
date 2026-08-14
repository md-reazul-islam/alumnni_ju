@props(['label' => null, 'name', 'type' => 'text', 'hint' => null, 'required' => false])

<div>
    @if ($label)
        <label for="{{ $name }}" class="form-label">
            {{ $label }} @if($required)<span class="text-red-500">*</span>@endif
        </label>
    @endif

    <input
        type="{{ $type }}"
        name="{{ $name }}"
        id="{{ $name }}"
        {{ $required ? 'required' : '' }}
        {{ $attributes->class(['form-input', 'border-red-400 focus:border-red-500 focus:ring-red-500' => $errors->has($name)]) }}
    />

    @if ($hint && !$errors->has($name))
        <p class="form-hint">{{ $hint }}</p>
    @endif

    @error($name)
        <p class="form-error">{{ $message }}</p>
    @enderror
</div>
