<x-layouts::admin :title="'Edit Story'">
    <x-breadcrumb :items="[['label' => 'Stories', 'url' => route('admin.stories.index')], ['label' => 'Edit']]" class="mb-4" />
    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Edit Story</h1>

    <form method="POST" action="{{ route('admin.stories.update', $story) }}" enctype="multipart/form-data" class="card card-body mt-6 space-y-5">
        @csrf
        @method('PUT')
        @include('admin.stories._form')

        <div class="flex justify-end gap-3">
            <x-button :href="route('admin.stories.index')" variant="secondary">Cancel</x-button>
            <x-button type="submit">Save Changes</x-button>
        </div>
    </form>
</x-layouts::admin>
