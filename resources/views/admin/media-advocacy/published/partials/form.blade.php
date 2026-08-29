@php $item ??= null; @endphp

<div class="grid grid-cols-1 gap-5 sm:grid-cols-2" x-data="{ type: '{{ old('type', $item?->type ?? 'image') }}' }">
    <x-input label="Title" name="title" :value="old('title', $item?->title)" required />
    <x-select label="Type" name="type" x-model="type" :selected="old('type', $item?->type ?? 'image')" :options="['image' => 'Image', 'video' => 'Video']" :placeholder="null" required />

    <div class="sm:col-span-2" x-show="type === 'image'">
        <label class="form-label">Image</label>
        @if ($item?->image)
            <img src="{{ $item->image_url }}" class="mb-3 h-24 w-auto rounded-lg object-cover">
        @endif
        <input type="file" name="image" accept="image/*" class="form-input">
        @error('image')
            <p class="form-error">{{ $message }}</p>
        @enderror
    </div>

    <div class="sm:col-span-2" x-show="type === 'video'" x-cloak>
        <x-input label="Video URL (YouTube or Vimeo)" name="video_url" type="url" placeholder="https://www.youtube.com/watch?v=..." :value="old('video_url', $item?->video_url)" />
    </div>

    <x-input label="Tag (optional)" name="tag" :value="old('tag', $item?->tag)" hint="e.g. product promotion, banner, poster, news, blog" />
</div>

<x-textarea label="Description (optional)" name="description" rows="3" class="mt-5">{{ old('description', $item?->description) }}</x-textarea>

<x-input label="Sort Order" name="sort_order" type="number" min="0" :value="old('sort_order', $item?->sort_order ?? 0)" class="mt-5 max-w-xs" />

@if ($item)
    <label class="mt-5 flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300">
        <input type="checkbox" name="is_active" value="1" @checked($item->is_active) class="rounded border-slate-300 text-navy-700 focus:ring-navy-500">
        Active
    </label>
@endif
