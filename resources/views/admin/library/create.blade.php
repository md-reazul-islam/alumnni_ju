<x-layouts::admin :title="'Add Book'">
    <x-breadcrumb :items="[['label' => 'Library', 'url' => route('admin.library.index')], ['label' => 'Add Book']]" class="mb-4" />
    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Add Book</h1>
    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Books you add here are published immediately.</p>

    <form method="POST" action="{{ route('admin.library.store') }}" enctype="multipart/form-data" class="card card-body mt-6 space-y-5">
        @csrf
        @include('admin.library._form')

        <div class="flex justify-end gap-3">
            <x-button :href="route('admin.library.index')" variant="secondary">Cancel</x-button>
            <x-button type="submit">Add Book</x-button>
        </div>
    </form>
</x-layouts::admin>
