<x-layouts::alumni :title="'Post a Trip'">
    <x-breadcrumb :items="[['label' => 'Driver Dashboard', 'url' => route('carpooling.driver.dashboard')], ['label' => 'Post a Trip']]" class="mb-4" />
    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Post a Trip</h1>

    <form method="POST" action="{{ route('carpooling.schedules.store') }}" class="card card-body mt-6 space-y-5">
        @csrf
        @include('carpooling.driver.schedules.partials.form')
        <div class="flex justify-end"><x-button type="submit">Submit for Approval</x-button></div>
    </form>
</x-layouts::alumni>
