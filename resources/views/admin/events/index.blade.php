<x-layouts::admin :title="'Events'">
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Events</h1>
        <x-button :href="route('admin.events.create')" size="sm">
            <x-icon name="plus" class="h-4 w-4" /> Create Event
        </x-button>
    </div>

    <form method="GET" class="mt-6 flex flex-col gap-3 sm:flex-row">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search events" class="form-input sm:max-w-xs">
        <select name="status" class="form-select sm:max-w-xs">
            <option value="">All statuses</option>
            @foreach (['draft', 'published', 'scheduled', 'cancelled'] as $status)
                <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
            @endforeach
        </select>
        <x-button type="submit" variant="secondary">Filter</x-button>
    </form>

    @if ($events->isEmpty())
        <x-empty-state icon="calendar" title="No events found" class="mt-8" />
    @else
        <x-table class="mt-6">
            <thead>
                <tr>
                    <th>Event</th>
                    <th>Date</th>
                    <th>Category</th>
                    <th>Status</th>
                    <th>Registrations</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($events as $event)
                    <tr>
                        <td class="font-medium text-slate-900 dark:text-white">{{ $event->title }}</td>
                        <td>{{ $event->event_date->format('M d, Y') }}</td>
                        <td>{{ ucfirst(str_replace('_', ' ', $event->category)) }}</td>
                        <td>
                            <x-badge :variant="match($event->status) { 'published' => 'success', 'draft' => 'neutral', 'scheduled' => 'info', 'cancelled' => 'danger', default => 'neutral' }">
                                {{ ucfirst($event->status) }}
                            </x-badge>
                        </td>
                        <td>{{ $event->registrations_count }}</td>
                        <td>
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.events.attendees', $event) }}" class="text-slate-400 hover:text-navy-700" title="Attendees"><x-icon name="users" class="h-4 w-4" /></a>
                                <a href="{{ route('admin.events.edit', $event) }}" class="text-slate-400 hover:text-navy-700" title="Edit"><x-icon name="pencil" class="h-4 w-4" /></a>
                                <form method="POST" action="{{ route('admin.events.destroy', $event) }}" onsubmit="event.preventDefault(); confirmDelete(this, '{{ $event->title }}');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-slate-400 hover:text-red-600" title="Delete"><x-icon name="trash-2" class="h-4 w-4" /></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </x-table>

        <div class="mt-6">{{ $events->links() }}</div>
    @endif

    @push('scripts')
    <script>
        function confirmDelete(form, title) {
            Swal.fire({
                title: `Delete "${title}"?`,
                text: 'This action cannot be undone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Delete',
                confirmButtonColor: '#dc2626',
            }).then((result) => { if (result.isConfirmed) form.submit(); });
        }
    </script>
    @endpush
</x-layouts::admin>
