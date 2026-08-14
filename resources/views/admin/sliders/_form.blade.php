@php $slider = $slider ?? null; @endphp

<div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
    <div class="sm:col-span-2">
        <x-input label="Title" name="title" :value="old('title', $slider?->title)" required />
    </div>

    <div class="sm:col-span-2">
        <x-input label="Subtitle" name="subtitle" :value="old('subtitle', $slider?->subtitle)" hint="Short supporting line shown under the title." />
    </div>

    <div class="sm:col-span-2">
        <label class="form-label">Slide Image {{ $slider ? '' : '*' }}</label>
        <input type="file" name="image" accept="image/*" class="form-input" {{ $slider ? '' : 'required' }}>
        <p class="form-hint">Recommended: 1920x800px or wider, landscape orientation.</p>
        @if ($slider?->image_url)
            <img src="{{ $slider->image_url }}" class="mt-2 h-24 w-full rounded-lg object-cover sm:w-64">
        @endif
        @error('image')
            <p class="form-error">{{ $message }}</p>
        @enderror
    </div>

    <x-input label="Button Text" name="button_text" :value="old('button_text', $slider?->button_text)" hint="Leave blank to hide the button." />
    <x-input label="Button URL" name="button_url" :value="old('button_url', $slider?->button_url)" />

    <x-input label="Position" name="position" type="number" min="0" :value="old('position', $slider?->position)" hint="Lower numbers appear first." />

    <div class="flex items-center gap-2 pt-7">
        <input type="hidden" name="is_active" value="0">
        <input type="checkbox" name="is_active" id="is_active" value="1" class="rounded border-slate-300 text-navy-700 focus:ring-navy-500" @checked(old('is_active', $slider?->is_active ?? true))>
        <label for="is_active" class="form-label !mb-0">Active (visible on homepage)</label>
    </div>
</div>
