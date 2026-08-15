@props(['label' => null, 'name', 'rows' => 4, 'required' => false, 'bag' => 'default'])

<div>
    @if ($label)
        <label for="{{ $name }}" class="form-label">
            {{ $label }} @if($required)<span class="text-red-500">*</span>@endif
        </label>
    @endif

    <textarea
        name="{{ $name }}"
        id="{{ $name }}"
        rows="{{ $rows }}"
        {{ $required ? 'required' : '' }}
        {{ $attributes->class(['form-textarea', 'border-red-400 focus:border-red-500 focus:ring-red-500' => $errors->getBag($bag)->has($name)]) }}
    >{{ $slot }}</textarea>

    @error($name, $bag)
        <p class="form-error">{{ $message }}</p>
    @enderror
</div>
