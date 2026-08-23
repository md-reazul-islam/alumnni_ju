<x-layouts::alumni :title="'Edit Trip'">
    <x-breadcrumb :items="[['label' => 'Driver Dashboard', 'url' => route('carpooling.driver.dashboard')], ['label' => 'Edit Trip']]" class="mb-4" />
    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Edit Trip</h1>

    <form method="POST" action="{{ route('carpooling.schedules.update', $schedule) }}" class="card card-body mt-6 space-y-5">
        @csrf
        @method('PUT')
        @include('carpooling.driver.schedules.partials.form')
        <div class="flex justify-end"><x-button type="submit">Save Changes</x-button></div>
    </form>

    @if (! $schedule->isLocked() && $schedule->status !== 'cancelled')
        <form method="POST" action="{{ route('carpooling.schedules.cancel', $schedule) }}" class="mt-4" onsubmit="return confirm('Cancel this trip?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="text-sm font-medium text-red-600 hover:underline">Cancel this trip</button>
        </form>
    @endif
</x-layouts::alumni>
