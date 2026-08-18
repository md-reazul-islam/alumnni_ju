@php $photo = $photo ?? null; @endphp

<div>
    <label class="form-label">Photo @if (!$photo)<span class="text-red-500">*</span>@endif</label>
    @if ($photo?->image)
        <img src="{{ $photo->image_url }}" class="mb-3 h-40 w-auto rounded-lg object-cover" alt="{{ $photo->description }}">
    @endif
    <input type="file" name="image" accept="image/*" @if (!$photo) required @endif class="form-input">
    @if ($photo)
        <p class="form-hint">Leave blank to keep the current photo.</p>
    @endif
    @error('image')
        <p class="form-error">{{ $message }}</p>
    @enderror
</div>

<x-textarea label="Description" name="description" rows="3">{{ old('description', $photo?->description) }}</x-textarea>
