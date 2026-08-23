<x-layouts::alumni :title="'Add a Car'">
    <x-breadcrumb :items="[['label' => 'My Cars', 'url' => route('carpooling.cars.index')], ['label' => 'Add a Car']]" class="mb-4" />
    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Add a Car</h1>

    <form method="POST" action="{{ route('carpooling.cars.store') }}" enctype="multipart/form-data" class="card card-body mt-6 space-y-5">
        @csrf
        @include('carpooling.driver.cars.partials.form')
        <div class="flex justify-end"><x-button type="submit">Save Car</x-button></div>
    </form>
</x-layouts::alumni>
