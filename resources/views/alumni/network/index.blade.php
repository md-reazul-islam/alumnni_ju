<x-layouts::alumni :title="'My Network'">
    <div x-data="{ tab: 'connections' }">
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">My Network</h1>
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Manage your alumni connections and discover new ones.</p>

        <div class="mt-6 flex gap-1 border-b border-slate-200 dark:border-navy-800">
            @foreach (['connections' => 'Connections ('.$connections->total().')', 'received' => 'Requests ('.$received->count().')', 'sent' => 'Sent ('.$sent->count().')', 'suggestions' => 'Suggestions'] as $key => $label)
                <button @click="tab = '{{ $key }}'" :class="tab === '{{ $key }}' ? 'border-navy-700 text-navy-800 dark:text-white' : 'border-transparent text-slate-500 dark:text-slate-400'" class="border-b-2 px-4 py-2.5 text-sm font-medium">
                    {{ $label }}
                </button>
            @endforeach
        </div>

        {{-- Connections --}}
        <div x-show="tab === 'connections'" x-cloak class="mt-6">
            @if ($connections->isEmpty())
                <x-empty-state icon="users" title="No connections yet" description="Browse the directory or check suggestions to start connecting." />
            @else
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3" id="connections-list">
                    @foreach ($connections as $connection)
                        @php $person = $connection->requester_id === auth()->id() ? $connection->recipient : $connection->requester; @endphp
                        <div class="card card-body flex items-center gap-3" data-connection-id="{{ $connection->id }}">
                            <a href="{{ route('alumni.profile.show', $person) }}" class="flex min-w-0 flex-1 items-center gap-3">
                                <x-avatar :src="$person->avatar_url" :name="$person->full_name" />
                                <div class="min-w-0">
                                    <p class="truncate text-sm font-semibold text-slate-900 dark:text-white">{{ $person->full_name }}</p>
                                    <p class="truncate text-xs text-slate-500 dark:text-slate-400">{{ $person->alumniProfile?->degree?->abbreviation }} {{ $person->alumniProfile?->graduation_year }}</p>
                                </div>
                            </a>
                            <button
                                type="button"
                                class="flex-shrink-0 rounded-lg p-1.5 text-slate-400 hover:bg-red-50 hover:text-red-600 dark:hover:bg-red-900/20"
                                onclick="removeConnection({{ $connection->id }}, this)"
                            >
                                <x-icon name="x" class="h-4 w-4" />
                            </button>
                        </div>
                    @endforeach
                </div>
                <div class="mt-6">{{ $connections->onEachSide(1)->links() }}</div>
            @endif
        </div>

        {{-- Received requests --}}
        <div x-show="tab === 'received'" x-cloak class="mt-6">
            @if ($received->isEmpty())
                <x-empty-state icon="user-plus" title="No pending requests" />
            @else
                <div class="space-y-3" id="received-list">
                    @foreach ($received as $connection)
                        <div class="card card-body flex items-center gap-3" data-connection-id="{{ $connection->id }}">
                            <x-avatar :src="$connection->requester->avatar_url" :name="$connection->requester->full_name" />
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-900 dark:text-white">{{ $connection->requester->full_name }}</p>
                                <p class="truncate text-xs text-slate-500 dark:text-slate-400">wants to connect with you</p>
                            </div>
                            <div class="flex flex-shrink-0 gap-2">
                                <button onclick="respondToRequest({{ $connection->id }}, 'accept', this)" class="btn-primary btn-sm">Accept</button>
                                <button onclick="respondToRequest({{ $connection->id }}, 'reject', this)" class="btn-secondary btn-sm">Decline</button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Sent requests --}}
        <div x-show="tab === 'sent'" x-cloak class="mt-6">
            @if ($sent->isEmpty())
                <x-empty-state icon="send" title="No sent requests" />
            @else
                <div class="space-y-3" id="sent-list">
                    @foreach ($sent as $connection)
                        <div class="card card-body flex items-center gap-3" data-connection-id="{{ $connection->id }}">
                            <x-avatar :src="$connection->recipient->avatar_url" :name="$connection->recipient->full_name" />
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-900 dark:text-white">{{ $connection->recipient->full_name }}</p>
                                <p class="truncate text-xs text-slate-500 dark:text-slate-400">Request pending</p>
                            </div>
                            <button onclick="cancelRequest({{ $connection->id }}, this)" class="btn-secondary btn-sm flex-shrink-0">Cancel</button>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Suggestions --}}
        <div x-show="tab === 'suggestions'" x-cloak class="mt-6">
            @if ($suggestions->isEmpty())
                <x-empty-state icon="sparkles" title="No suggestions right now" />
            @else
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3" id="suggestions-list">
                    @foreach ($suggestions as $profile)
                        <div class="card card-body flex items-center gap-3" data-user-id="{{ $profile->user_id }}">
                            <a href="{{ route('alumni.profile.show', $profile->user) }}" class="flex min-w-0 flex-1 items-center gap-3">
                                <x-avatar :src="$profile->user->avatar_url" :name="$profile->user->full_name" />
                                <div class="min-w-0">
                                    <p class="truncate text-sm font-semibold text-slate-900 dark:text-white">{{ $profile->user->full_name }}</p>
                                    <p class="truncate text-xs text-slate-500 dark:text-slate-400">{{ $profile->degree?->abbreviation }} {{ $profile->graduation_year }}</p>
                                </div>
                            </a>
                            <button onclick="sendRequest({{ $profile->user_id }}, this)" class="btn-primary btn-sm flex-shrink-0">Connect</button>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
    <script>
        function csrfToken() {
            return AlumniNetwork.csrfToken();
        }

        function removeFromDom(el) {
            const card = el.closest('[data-connection-id], [data-user-id]');
            if (card) card.remove();
        }

        function sendRequest(userId, btn) {
            AlumniNetwork.sendConnectionRequest(userId, btn);
            btn.disabled = true;
            setTimeout(() => removeFromDom(btn), 1500);
        }

        function respondToRequest(connectionId, action, btn) {
            fetch(`{{ url('/network') }}/${connectionId}/${action}`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken(), 'Accept': 'application/json' },
            })
                .then((r) => r.json())
                .then((data) => {
                    Swal.fire({ icon: 'success', title: data.message, timer: 1500, showConfirmButton: false });
                    removeFromDom(btn);
                });
        }

        function cancelRequest(connectionId, btn) {
            fetch(`{{ url('/network') }}/${connectionId}/cancel`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': csrfToken(), 'Accept': 'application/json' },
            })
                .then((r) => r.json())
                .then((data) => {
                    Swal.fire({ icon: 'success', title: data.message, timer: 1500, showConfirmButton: false });
                    removeFromDom(btn);
                });
        }

        function removeConnection(connectionId, btn) {
            Swal.fire({
                title: 'Remove this connection?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Remove',
                confirmButtonColor: '#dc2626',
            }).then((result) => {
                if (!result.isConfirmed) return;
                fetch(`{{ url('/network') }}/${connectionId}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': csrfToken(), 'Accept': 'application/json' },
                })
                    .then((r) => r.json())
                    .then((data) => {
                        Swal.fire({ icon: 'success', title: data.message, timer: 1500, showConfirmButton: false });
                        removeFromDom(btn);
                    });
            });
        }
    </script>
    @endpush
</x-layouts::alumni>
