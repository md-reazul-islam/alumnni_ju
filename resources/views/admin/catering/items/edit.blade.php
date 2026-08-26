<x-layouts::admin :title="'Edit Food Item'">
    <x-breadcrumb :items="[['label' => 'Catering'], ['label' => 'Food Items', 'url' => route('admin.catering.items.index')], ['label' => 'Edit Item']]" class="mb-4" />
    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Edit Food Item</h1>

    <form method="POST" action="{{ route('admin.catering.items.update', $item) }}" enctype="multipart/form-data" class="card card-body mt-6 space-y-5">
        @csrf
        @method('PUT')
        @include('admin.catering.items.partials.form')
        <div class="flex justify-end"><x-button type="submit">Save Changes</x-button></div>
    </form>
</x-layouts::admin>
