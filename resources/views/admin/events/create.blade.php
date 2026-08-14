<x-layouts::admin :title="'Create Event'">
    <x-breadcrumb :items="[['label' => 'Events', 'url' => route('admin.events.index')], ['label' => 'Create']]" class="mb-4" />
    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Create Event</h1>

    <form method="POST" action="{{ route('admin.events.store') }}" enctype="multipart/form-data" class="card card-body mt-6">
        @csrf
        @include('admin.events._form')

        <div class="mt-6 flex justify-end gap-3">
            <x-button :href="route('admin.events.index')" variant="secondary">Cancel</x-button>
            <x-button type="submit">Create Event</x-button>
        </div>
    </form>
</x-layouts::admin>
