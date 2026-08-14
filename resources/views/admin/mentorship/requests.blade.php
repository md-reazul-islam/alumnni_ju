<x-layouts::admin :title="'Mentorship Requests'">
    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Mentorship Requests</h1>

    @if ($requests->isEmpty())
        <x-empty-state icon="handshake" title="No mentorship requests yet" class="mt-8" />
    @else
        <x-table class="mt-6">
            <thead><tr><th>Mentee</th><th>Mentor</th><th>Status</th><th>Date</th></tr></thead>
            <tbody>
                @foreach ($requests as $req)
                    <tr>
                        <td class="font-medium text-slate-900 dark:text-white">{{ $req->mentee->full_name }}</td>
                        <td>{{ $req->mentor->full_name }}</td>
                        <td><x-badge :variant="match($req->status) { 'accepted' => 'success', 'rejected' => 'danger', default => 'warning' }">{{ ucfirst($req->status) }}</x-badge></td>
                        <td>{{ $req->created_at->format('M d, Y') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </x-table>
        <div class="mt-6">{{ $requests->links() }}</div>
    @endif
</x-layouts::admin>
