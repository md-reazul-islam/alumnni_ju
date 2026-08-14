<x-layouts::alumni :title="'My Mentorships'">
    <div x-data="{ tab: 'received' }">
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">My Mentorships</h1>

        <div class="mt-6 flex gap-1 border-b border-slate-200 dark:border-navy-800">
            @foreach (['received' => 'Requests Received ('.$receivedRequests->count().')', 'sent' => 'Requests Sent', 'mentoring' => 'Mentoring ('.$activeAsMentor->count().')', 'mentee' => 'My Mentors ('.$activeAsMentee->count().')'] as $key => $label)
                <button @click="tab = '{{ $key }}'" :class="tab === '{{ $key }}' ? 'border-navy-700 text-navy-800 dark:text-white' : 'border-transparent text-slate-500 dark:text-slate-400'" class="border-b-2 px-4 py-2.5 text-sm font-medium">
                    {{ $label }}
                </button>
            @endforeach
        </div>

        <div x-show="tab === 'received'" x-cloak class="mt-6 space-y-3">
            @forelse ($receivedRequests as $req)
                <div class="card card-body flex items-center gap-3">
                    <x-avatar :src="$req->mentee->avatar_url" :name="$req->mentee->full_name" />
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-sm font-semibold text-slate-900 dark:text-white">{{ $req->mentee->full_name }}</p>
                        <p class="truncate text-xs text-slate-500 dark:text-slate-400">{{ $req->message ?: 'No message provided' }}</p>
                    </div>
                    <div class="flex flex-shrink-0 gap-2">
                        <form method="POST" action="{{ route('mentorship.accept', $req) }}">
                            @csrf
                            <button type="submit" class="btn-primary btn-sm">Accept</button>
                        </form>
                        <form method="POST" action="{{ route('mentorship.reject', $req) }}">
                            @csrf
                            <button type="submit" class="btn-secondary btn-sm">Decline</button>
                        </form>
                    </div>
                </div>
            @empty
                <x-empty-state icon="user-plus" title="No pending requests" />
            @endforelse
        </div>

        <div x-show="tab === 'sent'" x-cloak class="mt-6 space-y-3">
            @forelse ($sentRequests as $req)
                <div class="card card-body flex items-center gap-3">
                    <x-avatar :src="$req->mentor->avatar_url" :name="$req->mentor->full_name" />
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-sm font-semibold text-slate-900 dark:text-white">{{ $req->mentor->full_name }}</p>
                    </div>
                    <x-badge :variant="match($req->status) { 'accepted' => 'success', 'rejected' => 'danger', default => 'warning' }">{{ ucfirst($req->status) }}</x-badge>
                </div>
            @empty
                <x-empty-state icon="send" title="No requests sent" />
            @endforelse
        </div>

        <div x-show="tab === 'mentoring'" x-cloak class="mt-6 space-y-3">
            @forelse ($activeAsMentor as $m)
                <div class="card card-body flex items-center gap-3">
                    <x-avatar :src="$m->mentee->avatar_url" :name="$m->mentee->full_name" />
                    <p class="text-sm font-semibold text-slate-900 dark:text-white">{{ $m->mentee->full_name }}</p>
                    @if (Route::has('messages.create'))
                        <x-button :href="route('messages.create', $m->mentee)" variant="secondary" size="sm" class="ml-auto">Message</x-button>
                    @endif
                </div>
            @empty
                <x-empty-state icon="handshake" title="No active mentees" />
            @endforelse
        </div>

        <div x-show="tab === 'mentee'" x-cloak class="mt-6 space-y-3">
            @forelse ($activeAsMentee as $m)
                <div class="card card-body flex items-center gap-3">
                    <x-avatar :src="$m->mentor->avatar_url" :name="$m->mentor->full_name" />
                    <p class="text-sm font-semibold text-slate-900 dark:text-white">{{ $m->mentor->full_name }}</p>
                    @if (Route::has('messages.create'))
                        <x-button :href="route('messages.create', $m->mentor)" variant="secondary" size="sm" class="ml-auto">Message</x-button>
                    @endif
                </div>
            @empty
                <x-empty-state icon="handshake" title="No active mentors" />
            @endforelse
        </div>
    </div>
</x-layouts::alumni>
