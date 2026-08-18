<x-layouts::alumni :title="'Add a Photo'">
    <x-breadcrumb :items="[['label' => 'Gallery', 'url' => route('gallery.mine')], ['label' => 'Add a Photo']]" class="mb-4" />
    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Add a Photo</h1>
    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Your photo will be reviewed by the alumni office before it appears in the public gallery.</p>

    <form method="POST" action="{{ route('gallery.store') }}" enctype="multipart/form-data" class="card card-body mt-6 space-y-5">
        @csrf

        <div>
            <label class="form-label">Photo <span class="text-red-500">*</span></label>
            <input type="file" name="image" accept="image/*" required class="form-input">
            @error('image')
                <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        <x-textarea label="Description" name="description" rows="3" placeholder="Say a bit about this photo...">{{ old('description') }}</x-textarea>

        <div class="flex justify-end">
            <x-button type="submit">Submit for Review</x-button>
        </div>
    </form>
</x-layouts::alumni>
