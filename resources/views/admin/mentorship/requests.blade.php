<x-layouts::admin :title="'Mentorship Requests'">
    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Mentorship Requests</h1>

    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">A mentorship only becomes active once both the mentor and the admin office have approved it.</p>

    @if ($requests->isEmpty())
        <x-empty-state icon="handshake" title="No mentorship requests yet" class="mt-8" />
    @else
        <x-table class="mt-6">
            <thead><tr><th>Mentee</th><th>Mentor</th><th>Mentor Status</th><th>Admin Status</th><th>Active</th><th>Date</th><th></th></tr></thead>
            <tbody>
                @foreach ($requests as $req)
                    <tr>
                        <td class="font-medium text-slate-900 dark:text-white">{{ $req->mentee->full_name }}</td>
                        <td>{{ $req->mentor->full_name }}</td>
                        <td><x-badge :variant="match($req->status) { 'accepted' => 'success', 'rejected' => 'danger', default => 'warning' }">{{ ucfirst($req->status) }}</x-badge></td>
                        <td><x-badge :variant="match($req->admin_status) { 'approved' => 'success', 'rejected' => 'danger', default => 'warning' }">{{ ucfirst($req->admin_status) }}</x-badge></td>
                        <td>
                            @if ($req->mentorship)
                                <x-badge :variant="match($req->mentorship->status) { 'active' => 'success', 'cancelled' => 'danger', default => 'neutral' }">{{ ucfirst($req->mentorship->status) }}</x-badge>
                            @else
                                <x-badge variant="neutral">Not yet</x-badge>
                            @endif
                        </td>
                        <td>{{ $req->created_at->format('M d, Y') }}</td>
                        <td>
                            @if ($req->admin_status === 'pending')
                                <div class="flex gap-2">
                                    <form method="POST" action="{{ route('admin.mentorship.requests.approve', $req) }}">
                                        @csrf
                                        <button type="submit" class="btn-primary btn-sm">Accept</button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.mentorship.requests.reject', $req) }}">
                                        @csrf
                                        <button type="submit" class="btn-secondary btn-sm">Decline</button>
                                    </form>
                                </div>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </x-table>
        <div class="mt-6">{{ $requests->links() }}</div>
    @endif
</x-layouts::admin>
