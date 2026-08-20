@php
    $listing ??= null;
@endphp

<div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
    <x-select label="Category" name="marketplace_category_id" required
        :selected="old('marketplace_category_id', $listing?->marketplace_category_id)"
        :options="$categories->pluck('name', 'id')" />
    <x-input label="Title" name="title" :value="old('title', $listing?->title)" required />
</div>

<x-textarea label="Description" name="description" rows="6" required>{{ old('description', $listing?->description) }}</x-textarea>

<div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
    <x-input label="Price" name="price" type="number" step="0.01" min="0" :value="old('price', $listing?->price)" required />
    <x-select label="Price Unit" name="price_unit" required
        :selected="old('price_unit', $listing?->price_unit ?? 'total')"
        :options="['total' => 'Total Price', 'per_month' => 'Per Month', 'per_year' => 'Per Year']" />
    <x-input label="Video Link (optional)" name="video_url" type="url" placeholder="YouTube or Vimeo URL" :value="old('video_url', $listing?->video_url)" />
</div>

<div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
    <x-input label="Address" name="address" :value="old('address', $listing?->address)" required hint="Used to show a map preview — no need to include a map link." />
    <x-input label="City" name="city" :value="old('city', $listing?->city)" />
</div>

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

<div class="border-t border-slate-100 pt-5 dark:border-navy-800" x-data="{ details: @js(old('details', $listing?->details ?? [])) }">
    <label class="form-label">Additional Details</label>
    <p class="form-hint mb-3">Add anything buyers should know — bedrooms, size, condition, etc.</p>

    <div class="space-y-3">
        <template x-for="(detail, index) in details" :key="index">
            <div class="flex flex-col gap-3 sm:flex-row">
                <input type="text" :name="'details[' + index + '][label]'" x-model="detail.label" placeholder="Label (e.g. Bedrooms)" class="form-input sm:w-52">
                <input type="text" :name="'details[' + index + '][value]'" x-model="detail.value" placeholder="Value (e.g. 3)" class="form-input flex-1">
                <button type="button" @click="details.splice(index, 1)" class="text-slate-400 hover:text-red-500">
                    <x-icon name="trash-2" class="h-5 w-5" />
                </button>
            </div>
        </template>
    </div>

    <button type="button" @click="details.push({ label: '', value: '' })" class="btn-secondary mt-3">+ Add Detail</button>
</div>
