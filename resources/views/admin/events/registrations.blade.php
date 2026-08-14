<x-layouts::admin :title="'Event Registrations'">
    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Event Registrations</h1>
    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">All registrations across every event.</p>

    <form method="GET" class="mt-6 flex flex-col gap-3 sm:flex-row">
        <select name="event_id" class="form-select sm:max-w-xs">
            <option value="">All events</option>
            @foreach ($events as $event)
                <option value="{{ $event->id }}" @selected(request('event_id') == $event->id)>{{ $event->title }}</option>
            @endforeach
        </select>
        <select name="status" class="form-select sm:max-w-xs">
            <option value="">All statuses</option>
            @foreach (['registered', 'cancelled', 'attended'] as $status)
                <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
            @endforeach
        </select>
        <x-button type="submit" variant="secondary">Filter</x-button>
    </form>

    @if ($registrations->isEmpty())
        <x-empty-state icon="clipboard-list" title="No registrations found" class="mt-8" />
    @else
        <x-table class="mt-6">
            <thead>
                <tr><th>Alumnus</th><th>Event</th><th>Status</th><th>Registered</th></tr>
            </thead>
            <tbody>
                @foreach ($registrations as $registration)
                    <tr>
                        <td class="font-medium text-slate-900 dark:text-white">{{ $registration->user->full_name }}</td>
                        <td>{{ $registration->event->title }}</td>
                        <td><x-badge :variant="$registration->status === 'registered' ? 'success' : 'neutral'">{{ ucfirst($registration->status) }}</x-badge></td>
                        <td>{{ $registration->registered_at?->format('M d, Y') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </x-table>

        <div class="mt-6">{{ $registrations->links() }}</div>
    @endif
</x-layouts::admin>
