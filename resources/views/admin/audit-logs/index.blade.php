<x-layouts::admin :title="'Audit Logs'">
    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Audit Logs</h1>
    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">A complete record of administrative actions across the platform.</p>

    <form method="GET" class="mt-6 flex flex-col gap-3 sm:flex-row">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search description" class="form-input sm:max-w-xs">
        <select name="action" class="form-select sm:max-w-xs">
            <option value="">All actions</option>
            @foreach ($actions as $action)
                <option value="{{ $action }}" @selected(request('action') === $action)>{{ ucfirst(str_replace('_', ' ', $action)) }}</option>
            @endforeach
        </select>
        <x-button type="submit" variant="secondary">Filter</x-button>
    </form>

    @if ($logs->isEmpty())
        <x-empty-state icon="clipboard-list" title="No audit log entries yet" class="mt-8" />
    @else
        <x-table class="mt-6">
            <thead><tr><th>Action</th><th>Performed By</th><th>Description</th><th>IP Address</th><th>Date</th></tr></thead>
            <tbody>
                @foreach ($logs as $log)
                    <tr>
                        <td><x-badge variant="neutral">{{ ucfirst(str_replace('_', ' ', $log->action)) }}</x-badge></td>
                        <td class="font-medium text-slate-900 dark:text-white">{{ $log->user?->full_name ?? 'System' }}</td>
                        <td class="max-w-md truncate">{{ $log->description }}</td>
                        <td>{{ $log->ip_address }}</td>
                        <td>{{ $log->created_at->format('M d, Y g:i A') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </x-table>
        <div class="mt-6">{{ $logs->links() }}</div>
    @endif
</x-layouts::admin>
