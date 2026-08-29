<x-layouts::admin :title="'Edit Published Media'">
    <x-breadcrumb :items="[['label' => 'Media Advocacy'], ['label' => 'Published Media', 'url' => route('admin.media-advocacy.published.index')], ['label' => 'Edit Item']]" class="mb-4" />
    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Edit Published Media</h1>

    <form method="POST" action="{{ route('admin.media-advocacy.published.update', $item) }}" enctype="multipart/form-data" class="card card-body mt-6 space-y-5">
        @csrf
        @method('PUT')
        @include('admin.media-advocacy.published.partials.form')
        <div class="flex justify-end"><x-button type="submit">Save Changes</x-button></div>
    </form>
</x-layouts::admin>
