<x-layouts::admin :title="'Approved Trips'">
    <x-breadcrumb :items="[['label' => 'Carpooling'], ['label' => 'Approved Trips']]" class="mb-4" />
    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Approved Trips</h1>

    @if (session('status'))
        <x-alert variant="success" class="mt-4">{{ session('status') }}</x-alert>
    @endif

    @if ($schedules->isEmpty())
        <x-empty-state icon="car" title="No approved trips yet" class="mt-8" />
    @else
        <x-table class="mt-6">
            <thead><tr><th>Route</th><th>Driver</th><th>Departure</th><th>Price/Seat</th><th>Seats</th><th>Status</th></tr></thead>
            <tbody>
                @foreach ($schedules as $schedule)
                    <tr>
                        <td class="font-medium text-slate-900 dark:text-white">
                            <a href="{{ route('admin.carpooling.schedules.show', $schedule) }}" class="hover:underline">{{ $schedule->origin }} &rarr; {{ $schedule->destination }}</a>
                        </td>
                        <td>{{ $schedule->driverProfile->user->full_name }}</td>
                        <td>{{ $schedule->departure_date->format('M j, Y') }} {{ \Illuminate\Support\Carbon::parse($schedule->departure_time)->format('g:i A') }}</td>
                        <td>${{ number_format($schedule->price_per_seat, 2) }}</td>
                        <td>{{ $schedule->seats_booked }}/{{ $schedule->seats_offered }}</td>
                        <td><x-badge :variant="$schedule->status === 'completed' ? 'info' : 'success'">{{ ucfirst($schedule->status) }}</x-badge></td>
                    </tr>
                @endforeach
            </tbody>
        </x-table>
        <div class="mt-6">{{ $schedules->links() }}</div>
    @endif
</x-layouts::admin>
