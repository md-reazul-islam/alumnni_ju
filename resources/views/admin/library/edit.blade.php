<x-layouts::admin :title="'Edit Book'">
    <x-breadcrumb :items="[['label' => 'Library', 'url' => route('admin.library.index')], ['label' => 'Edit']]" class="mb-4" />
    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Edit Book</h1>

    <form method="POST" action="{{ route('admin.library.update', $book) }}" enctype="multipart/form-data" class="card card-body mt-6 space-y-5">
        @csrf
        @method('PUT')
        @include('admin.library._form')

        <div class="flex justify-end gap-3">
            <x-button :href="route('admin.library.index')" variant="secondary">Cancel</x-button>
            <x-button type="submit">Save Changes</x-button>
        </div>
    </form>
</x-layouts::admin>
