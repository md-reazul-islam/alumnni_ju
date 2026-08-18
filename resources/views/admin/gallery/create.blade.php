<x-layouts::admin :title="'Add Gallery Photo'">
    <x-breadcrumb :items="[['label' => 'Gallery', 'url' => route('admin.gallery.index')], ['label' => 'Add Photo']]" class="mb-4" />
    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Add Gallery Photo</h1>
    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Photos you add here are published immediately.</p>

    <form method="POST" action="{{ route('admin.gallery.store') }}" enctype="multipart/form-data" class="card card-body mt-6 space-y-5">
        @csrf
        @include('admin.gallery._form')

        <div class="flex justify-end gap-3">
            <x-button :href="route('admin.gallery.index')" variant="secondary">Cancel</x-button>
            <x-button type="submit">Add Photo</x-button>
        </div>
    </form>
</x-layouts::admin>
