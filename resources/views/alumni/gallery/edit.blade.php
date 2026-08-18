<x-layouts::alumni :title="'Edit Photo'">
    <x-breadcrumb :items="[['label' => 'Gallery', 'url' => route('gallery.mine')], ['label' => 'Edit']]" class="mb-4" />
    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Edit Photo</h1>
    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Saving changes will resubmit this photo for admin review before it's visible again.</p>

    <form method="POST" action="{{ route('gallery.update', $photo) }}" enctype="multipart/form-data" class="card card-body mt-6 space-y-5">
        @csrf
        @method('PUT')

        <div>
            <label class="form-label">Photo</label>
            <img src="{{ $photo->image_url }}" class="mb-3 h-40 w-auto rounded-lg object-cover" alt="{{ $photo->description }}">
            <input type="file" name="image" accept="image/*" class="form-input">
            <p class="form-hint">Leave blank to keep the current photo.</p>
            @error('image')
                <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        <x-textarea label="Description" name="description" rows="3">{{ old('description', $photo->description) }}</x-textarea>

        <div class="flex justify-end gap-3">
            <x-button :href="route('gallery.mine')" variant="secondary">Cancel</x-button>
            <x-button type="submit">Save Changes</x-button>
        </div>
    </form>
</x-layouts::alumni>
