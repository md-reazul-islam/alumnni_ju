<x-layouts::admin :title="'Rejected Trips'">
    <x-breadcrumb :items="[['label' => 'Carpooling'], ['label' => 'Rejected Trips']]" class="mb-4" />
    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Rejected Trip Schedules</h1>

    @if ($schedules->isEmpty())
        <x-empty-state icon="ban" title="No rejected trips" class="mt-8" />
    @else
        <x-table class="mt-6">
            <thead><tr><th>Route</th><th>Driver</th><th>Reason</th><th></th></tr></thead>
            <tbody>
                @foreach ($schedules as $schedule)
                    <tr>
                        <td class="font-medium text-slate-900 dark:text-white">
                            <a href="{{ route('admin.carpooling.schedules.show', $schedule) }}" class="hover:underline">{{ $schedule->origin }} &rarr; {{ $schedule->destination }}</a>
                        </td>
                        <td>{{ $schedule->driverProfile->user->full_name }}</td>
                        <td class="max-w-xs truncate" title="{{ $schedule->rejection_reason }}">{{ $schedule->rejection_reason }}</td>
                        <td><a href="{{ route('admin.carpooling.schedules.show', $schedule) }}" class="text-navy-700 hover:underline dark:text-navy-300">View</a></td>
                    </tr>
                @endforeach
            </tbody>
        </x-table>
        <div class="mt-6">{{ $schedules->links() }}</div>
    @endif
</x-layouts::admin>
