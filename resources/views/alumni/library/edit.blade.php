<x-layouts::alumni :title="'Edit Book'">
    <x-breadcrumb :items="[['label' => 'Library', 'url' => route('library.donations')], ['label' => 'Edit']]" class="mb-4" />
    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Edit Book</h1>
    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Saving changes will resubmit this book for admin review before it's visible again.</p>

    <form method="POST" action="{{ route('library.update', $book) }}" enctype="multipart/form-data" class="card card-body mt-6 space-y-5">
        @csrf
        @method('PUT')

        <x-input label="Title" name="title" :value="old('title', $book->title)" required />
        <x-input label="Author" name="author" :value="old('author', $book->author)" />

        <div>
            <label class="form-label">Cover Image</label>
            @if ($book->cover_url)
                <img src="{{ $book->cover_url }}" class="mb-3 h-40 w-auto rounded-lg object-cover" alt="{{ $book->title }}">
            @endif
            <input type="file" name="cover" accept="image/*" class="form-input">
            <p class="form-hint">Leave blank to keep the current cover.</p>
            @error('cover')
                <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        <x-textarea label="Description" name="description" rows="4">{{ old('description', $book->description) }}</x-textarea>

        <x-input label="Tags" name="tags" :value="old('tags', $book->tags)" placeholder="e.g. fiction, textbook, self-help" hint="Optional keywords to help others find this book in search. Separate multiple tags with commas." />

        <div class="flex justify-end gap-3">
            <x-button :href="route('library.donations')" variant="secondary">Cancel</x-button>
            <x-button type="submit">Save Changes</x-button>
        </div>
    </form>
</x-layouts::alumni>
