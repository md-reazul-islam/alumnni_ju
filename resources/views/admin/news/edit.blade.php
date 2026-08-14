<x-layouts::admin :title="'Edit Article'">
    <x-breadcrumb :items="[['label' => 'News', 'url' => route('admin.news.index')], ['label' => 'Edit']]" class="mb-4" />
    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Edit Article</h1>

    <form method="POST" action="{{ route('admin.news.update', $news) }}" enctype="multipart/form-data" class="card card-body mt-6">
        @csrf @method('PUT')
        @include('admin.news._form')
        <div class="mt-6 flex justify-end gap-3">
            <x-button :href="route('admin.news.index')" variant="secondary">Cancel</x-button>
            <x-button type="submit">Save Changes</x-button>
        </div>
    </form>
</x-layouts::admin>
