@php
    $listing ??= null;
@endphp

<div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
    <x-select label="Category" name="catering_homemade_category_id" required
        :selected="old('catering_homemade_category_id', $listing?->catering_homemade_category_id)"
        :options="$categories->pluck('name', 'id')" />
    <x-input label="Title" name="title" :value="old('title', $listing?->title)" required />
</div>

<x-textarea label="Description" name="description" rows="6" required>{{ old('description', $listing?->description) }}</x-textarea>

<div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
    <x-input label="Price" name="price" type="number" step="0.01" min="0" :value="old('price', $listing?->price)" required />
    <x-select label="Price Unit" name="price_unit" required
        :selected="old('price_unit', $listing?->price_unit ?? 'per_item')"
        :options="['per_item' => 'Per Item', 'per_box' => 'Per Box', 'per_dozen' => 'Per Dozen', 'total' => 'Total Price']" />
</div>

<x-input label="Tags" name="tags" :value="old('tags', $listing?->tags)" placeholder="e.g. spicy, vegan, no-nuts" hint="Optional keywords to help buyers find this listing in search. Separate multiple tags with commas." />

<div class="border-t border-slate-100 pt-5 dark:border-navy-800">
    <label class="form-label">Photos</label>
    <p class="form-hint mb-2">Up to 8 images (jpg, png, webp).</p>

    @if ($listing && $listing->images->isNotEmpty())
        <div class="mb-3 grid grid-cols-2 gap-3 sm:grid-cols-4">
            @foreach ($listing->images as $image)
                <label class="group relative block overflow-hidden rounded-lg border border-slate-200 dark:border-navy-700">
                    <img src="{{ asset('storage/' . $image->path) }}" class="h-24 w-full object-cover">
                    <span class="absolute inset-x-0 bottom-0 flex items-center justify-center gap-1.5 bg-black/60 py-1 text-xs text-white">
                        <input type="checkbox" name="remove_images[]" value="{{ $image->id }}" class="rounded">
                        Remove
                    </span>
                </label>
            @endforeach
        </div>
    @endif

    <input type="file" name="images[]" accept="image/*" multiple {{ $listing ? '' : 'required' }} class="form-input">
    @error('images')
        <p class="form-error">{{ $message }}</p>
    @enderror
</div>
