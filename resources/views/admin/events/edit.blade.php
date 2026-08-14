<x-layouts::admin :title="'Edit Event'">
    <x-breadcrumb :items="[['label' => 'Events', 'url' => route('admin.events.index')], ['label' => 'Edit']]" class="mb-4" />
    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Edit Event</h1>

    <form method="POST" action="{{ route('admin.events.update', $event) }}" enctype="multipart/form-data" class="card card-body mt-6">
        @csrf
        @method('PUT')
        @include('admin.events._form')

        <div class="mt-6 flex justify-end gap-3">
            <x-button :href="route('admin.events.index')" variant="secondary">Cancel</x-button>
            <x-button type="submit">Save Changes</x-button>
        </div>
    </form>
</x-layouts::admin>
