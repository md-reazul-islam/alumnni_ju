<x-layouts::alumni :title="'Donate a Book'">
    <x-breadcrumb :items="[['label' => 'Library', 'url' => route('library.donations')], ['label' => 'Donate a Book']]" class="mb-4" />
    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Donate a Book</h1>
    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Your donation will be reviewed by the alumni office before it appears in Your Library.</p>

    <form method="POST" action="{{ route('library.store') }}" enctype="multipart/form-data" class="card card-body mt-6 space-y-5">
        @csrf

        <x-input label="Title" name="title" :value="old('title')" required />
        <x-input label="Author" name="author" :value="old('author')" />

        <div>
            <label class="form-label">Cover Image</label>
            <input type="file" name="cover" accept="image/*" class="form-input">
            @error('cover')
                <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        <x-textarea label="Description" name="description" rows="4" placeholder="Say a bit about this book...">{{ old('description') }}</x-textarea>

        <div class="flex justify-end">
            <x-button type="submit">Submit for Review</x-button>
        </div>
    </form>
</x-layouts::alumni>
