<x-layouts::admin :title="'Mentors'">
    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Mentors</h1>

    @if ($mentors->isEmpty())
        <x-empty-state icon="handshake" title="No mentors yet" class="mt-8" />
    @else
        <x-table class="mt-6">
            <thead><tr><th>Name</th><th>Expertise</th><th>Industry</th><th>Status</th></tr></thead>
            <tbody>
                @foreach ($mentors as $mentor)
                    <tr>
                        <td class="font-medium text-slate-900 dark:text-white">{{ $mentor->user->full_name }}</td>
                        <td>{{ $mentor->expertise }}</td>
                        <td>{{ $mentor->industry }}</td>
                        <td><x-badge :variant="$mentor->is_active ? 'success' : 'neutral'">{{ $mentor->is_active ? 'Active' : 'Inactive' }}</x-badge></td>
                    </tr>
                @endforeach
            </tbody>
        </x-table>
        <div class="mt-6">{{ $mentors->links() }}</div>
    @endif
</x-layouts::admin>
