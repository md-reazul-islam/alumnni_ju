@php $item ??= null; $selectedCategoryIds ??= []; @endphp

<div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
    <x-input label="Item Name" name="name" :value="old('name', $item?->name)" required />
    <x-input label="Base Price (USD)" name="base_price" type="number" step="0.01" min="0.01" :value="old('base_price', $item?->base_price)" required />
</div>

<x-input label="Unit Label" name="unit_label" :value="old('unit_label', $item?->unit_label ?? 'per plate')" hint="e.g. per plate, per tray, per dozen" required />

<x-textarea label="Description" name="description" rows="3">{{ old('description', $item?->description) }}</x-textarea>

<div>
    <label class="form-label">Program Categories</label>
    <div class="mt-1 grid grid-cols-2 gap-2 rounded-lg border border-slate-200 p-3 dark:border-navy-800 sm:grid-cols-3">
        @foreach ($categories as $category)
            <label class="flex items-center gap-2 text-sm text-slate-700 dark:text-slate-300">
                <input type="checkbox" name="category_ids[]" value="{{ $category->id }}" @checked(in_array($category->id, old('category_ids', $selectedCategoryIds))) class="rounded border-slate-300 text-navy-700 focus:ring-navy-500">
                {{ $category->name }}
            </label>
        @endforeach
    </div>
    @error('category_ids')
        <p class="form-error">{{ $message }}</p>
    @enderror
</div>

<div>
    <label class="form-label">Photo</label>
    @if ($item?->image)
        <img src="{{ $item->image_url }}" class="mb-3 h-24 w-auto rounded-lg object-cover">
    @endif
    <input type="file" name="image" accept="image/*" class="form-input">
    @error('image')
        <p class="form-error">{{ $message }}</p>
    @enderror
</div>

@if ($item)
    <label class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300">
        <input type="checkbox" name="is_active" value="1" @checked($item->is_active) class="rounded border-slate-300 text-navy-700 focus:ring-navy-500">
        Active
    </label>
@endif
