<x-layouts::admin :title="'Add Story'">
    <x-breadcrumb :items="[['label' => 'Stories', 'url' => route('admin.stories.index')], ['label' => 'Add Story']]" class="mb-4" />
    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Add Story</h1>
    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Create a story on behalf of a verified alumnus.</p>

    <form method="POST" action="{{ route('admin.stories.store') }}" enctype="multipart/form-data" class="card card-body mt-6 space-y-5">
        @csrf
        @include('admin.stories._form')

        <div class="flex justify-end gap-3">
            <x-button :href="route('admin.stories.index')" variant="secondary">Cancel</x-button>
            <x-button type="submit">Save Story</x-button>
        </div>
    </form>
</x-layouts::admin>
