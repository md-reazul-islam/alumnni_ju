<x-layouts::alumni :title="'Edit Car'">
    <x-breadcrumb :items="[['label' => 'My Cars', 'url' => route('carpooling.cars.index')], ['label' => 'Edit Car']]" class="mb-4" />
    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Edit Car</h1>

    <form method="POST" action="{{ route('carpooling.cars.update', $car) }}" enctype="multipart/form-data" class="card card-body mt-6 space-y-5">
        @csrf
        @method('PUT')
        @include('carpooling.driver.cars.partials.form')
        <div class="flex justify-end"><x-button type="submit">Save Changes</x-button></div>
    </form>
</x-layouts::alumni>
