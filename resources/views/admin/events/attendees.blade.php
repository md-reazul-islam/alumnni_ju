<x-layouts::admin :title="'Attendees'">
    <x-breadcrumb :items="[['label' => 'Events', 'url' => route('admin.events.index')], ['label' => $event->title]]" class="mb-4" />

    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $event->title }}</h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ $attendees->total() }} registered attendees</p>
        </div>
        <x-button :href="route('admin.events.attendees.export', $event)" variant="secondary" size="sm">
            <x-icon name="download" class="h-4 w-4" /> Export CSV
        </x-button>
    </div>

    @if ($attendees->isEmpty())
        <x-empty-state icon="users" title="No attendees yet" class="mt-8" />
    @else
        <x-table class="mt-6">
            <thead><tr><th>Name</th><th>Email</th><th>Registered</th></tr></thead>
            <tbody>
                @foreach ($attendees as $registration)
                    <tr>
                        <td class="font-medium text-slate-900 dark:text-white">{{ $registration->user->full_name }}</td>
                        <td>{{ $registration->user->email }}</td>
                        <td>{{ $registration->registered_at?->format('M d, Y g:i A') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </x-table>

        <div class="mt-6">{{ $attendees->links() }}</div>
    @endif
</x-layouts::admin>
