<x-layouts::admin :title="'Create Article'">
    <x-breadcrumb :items="[['label' => 'News', 'url' => route('admin.news.index')], ['label' => 'Create']]" class="mb-4" />
    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Create Article</h1>

    <form method="POST" action="{{ route('admin.news.store') }}" enctype="multipart/form-data" class="card card-body mt-6">
        @csrf
        @include('admin.news._form')
        <div class="mt-6 flex justify-end gap-3">
            <x-button :href="route('admin.news.index')" variant="secondary">Cancel</x-button>
            <x-button type="submit">Save Article</x-button>
        </div>
    </form>
</x-layouts::admin>
