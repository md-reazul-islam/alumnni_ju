<x-layouts::admin :title="'Edit Gallery Photo'">
    <x-breadcrumb :items="[['label' => 'Gallery', 'url' => route('admin.gallery.index')], ['label' => 'Edit']]" class="mb-4" />
    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Edit Gallery Photo</h1>

    <form method="POST" action="{{ route('admin.gallery.update', $photo) }}" enctype="multipart/form-data" class="card card-body mt-6 space-y-5">
        @csrf
        @method('PUT')
        @include('admin.gallery._form')

        <div class="flex justify-end gap-3">
            <x-button :href="route('admin.gallery.index')" variant="secondary">Cancel</x-button>
            <x-button type="submit">Save Changes</x-button>
        </div>
    </form>
</x-layouts::admin>
