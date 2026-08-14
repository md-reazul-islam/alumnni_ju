@php $news = $news ?? null; @endphp

<div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
    <div class="sm:col-span-2">
        <x-input label="Title" name="title" :value="old('title', $news?->title)" required />
    </div>

    <x-select label="Category" name="news_category_id" :selected="old('news_category_id', $news?->news_category_id)" :options="$categories->pluck('name', 'id')" placeholder="Select category" />

    <x-select label="Status" name="status" :selected="old('status', $news?->status ?? 'draft')" required :options="[
        'draft' => 'Draft', 'pending' => 'Pending Review', 'published' => 'Published', 'scheduled' => 'Scheduled', 'archived' => 'Archived',
    ]" />

    <div class="sm:col-span-2">
        <x-textarea label="Excerpt" name="excerpt" rows="2">{{ old('excerpt', $news?->excerpt) }}</x-textarea>
    </div>

    <div class="sm:col-span-2">
        <x-textarea label="Body" name="body" rows="10" required>{{ old('body', $news?->body) }}</x-textarea>
    </div>

    <div>
        <label class="form-label">Featured Image</label>
        <input type="file" name="featured_image" accept="image/*" class="form-input">
        @if ($news?->featured_image_url)
            <img src="{{ $news->featured_image_url }}" class="mt-2 h-20 rounded-lg object-cover">
        @endif
    </div>

    <x-input label="Tags (comma separated)" name="tags" :value="old('tags', $news?->tags?->pluck('name')->implode(', '))" />

    <div>
        <label class="form-label">Publish Date/Time</label>
        <input type="text" name="published_at" value="{{ old('published_at', $news?->published_at?->format('Y-m-d H:i')) }}" class="form-input flatpickr-publish" autocomplete="off">
    </div>
</div>

@push('scripts')
<script>flatpickr('.flatpickr-publish', { enableTime: true, dateFormat: 'Y-m-d H:i' });</script>
@endpush
