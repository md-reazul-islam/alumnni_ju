<x-layouts::admin :title="'Edit Slide'">
    <x-breadcrumb :items="[['label' => 'Homepage Slider', 'url' => route('admin.sliders.index')], ['label' => 'Edit']]" class="mb-4" />
    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Edit Slide</h1>

    <form method="POST" action="{{ route('admin.sliders.update', $slider) }}" enctype="multipart/form-data" class="card card-body mt-6">
        @csrf
        @method('PUT')
        @include('admin.sliders._form')

        <div class="mt-6 flex justify-end gap-3">
            <x-button :href="route('admin.sliders.index')" variant="secondary">Cancel</x-button>
            <x-button type="submit">Save Changes</x-button>
        </div>
    </form>
</x-layouts::admin>
