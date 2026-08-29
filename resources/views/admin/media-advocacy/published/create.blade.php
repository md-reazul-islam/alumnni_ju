<x-layouts::admin :title="'Add Published Media'">
    <x-breadcrumb :items="[['label' => 'Media Advocacy'], ['label' => 'Published Media', 'url' => route('admin.media-advocacy.published.index')], ['label' => 'Add Item']]" class="mb-4" />
    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Add Published Media</h1>

    <form method="POST" action="{{ route('admin.media-advocacy.published.store') }}" enctype="multipart/form-data" class="card card-body mt-6 space-y-5">
        @csrf
        @include('admin.media-advocacy.published.partials.form')
        <div class="flex justify-end"><x-button type="submit">Save</x-button></div>
    </form>
</x-layouts::admin>
