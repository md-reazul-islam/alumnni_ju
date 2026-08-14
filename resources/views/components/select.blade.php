@props(['label' => null, 'name', 'options' => [], 'placeholder' => 'Select an option', 'required' => false, 'selected' => null])

<div>
    @if ($label)
        <label for="{{ $name }}" class="form-label">
            {{ $label }} @if($required)<span class="text-red-500">*</span>@endif
        </label>
    @endif

    <select
        name="{{ $name }}"
        id="{{ $name }}"
        {{ $required ? 'required' : '' }}
        {{ $attributes->class(['form-select', 'border-red-400 focus:border-red-500 focus:ring-red-500' => $errors->has($name)]) }}
    >
        @if ($placeholder)
            <option value="">{{ $placeholder }}</option>
        @endif

        @foreach ($options as $value => $text)
            <option value="{{ $value }}" @selected(old($name, $selected) == $value)>{{ $text }}</option>
        @endforeach
    </select>

    @error($name)
        <p class="form-error">{{ $message }}</p>
    @enderror
</div>
