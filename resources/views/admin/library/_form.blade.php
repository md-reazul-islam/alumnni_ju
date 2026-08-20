@php $book = $book ?? null; @endphp

<x-input label="Title" name="title" :value="old('title', $book?->title)" required />
<x-input label="Author" name="author" :value="old('author', $book?->author)" />

<div>
    <label class="form-label">Cover Image</label>
    @if ($book?->cover_url)
        <img src="{{ $book->cover_url }}" class="mb-3 h-40 w-auto rounded-lg object-cover" alt="{{ $book->title }}">
    @endif
    <input type="file" name="cover" accept="image/*" class="form-input">
    @if ($book)
        <p class="form-hint">Leave blank to keep the current cover.</p>
    @endif
    @error('cover')
        <p class="form-error">{{ $message }}</p>
    @enderror
</div>

<x-textarea label="Description" name="description" rows="4">{{ old('description', $book?->description) }}</x-textarea>

<x-input label="Tags" name="tags" :value="old('tags', $book?->tags)" placeholder="e.g. fiction, textbook, self-help" hint="Optional keywords to help others find this book in search. Separate multiple tags with commas." />
