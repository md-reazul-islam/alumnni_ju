<x-layouts::alumni :title="'My Interests'">
    <x-breadcrumb :items="[['label' => 'My Interests']]" class="mb-4" />
    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">My Interests</h1>

    @if (session('status'))
        <x-alert variant="success" class="mt-4">{{ session('status') }}</x-alert>
    @endif

    <div x-data="{ tab: 'received' }" class="mt-6">
        <div class="flex gap-2 border-b border-slate-200 dark:border-navy-800">
            <button type="button" @click="tab = 'received'" :class="tab === 'received' ? 'border-navy-700 text-navy-700 dark:border-navy-300 dark:text-navy-300' : 'border-transparent text-slate-500'" class="border-b-2 px-4 py-2 text-sm font-medium">
                Received ({{ $received->count() }})
            </button>
            <button type="button" @click="tab = 'sent'" :class="tab === 'sent' ? 'border-navy-700 text-navy-700 dark:border-navy-300 dark:text-navy-300' : 'border-transparent text-slate-500'" class="border-b-2 px-4 py-2 text-sm font-medium">
                Sent ({{ $sent->count() }})
            </button>
        </div>

        <div x-show="tab === 'received'" class="mt-4 space-y-4">
            @forelse ($received as $interest)
                <div class="card card-body flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <p class="font-semibold text-slate-900 dark:text-white">{{ $interest->requester->full_name }}</p>
                        <p class="text-sm text-slate-500 dark:text-slate-400">Interested in "{{ $interest->profile->display_name }}"</p>
                        @if ($interest->requesterProfile)
                            <a href="{{ route('matrimony.show', $interest->requesterProfile) }}" class="text-xs text-navy-700 hover:underline dark:text-navy-300">View their profile &rarr;</a>
                        @endif
                        @if ($interest->message)
                            <p class="mt-1 text-sm italic text-slate-500 dark:text-slate-400">"{{ $interest->message }}"</p>
                        @endif
                    </div>
                    <div class="flex items-center gap-2">
                        <x-badge :variant="match($interest->status) { 'accepted' => 'success', 'declined' => 'danger', default => 'warning' }">
                            {{ ucfirst($interest->status) }}
                        </x-badge>
                        @if ($interest->status === 'pending')
                            <form method="POST" action="{{ route('matrimony.interests.accept', $interest) }}">
                                @csrf
                                <button type="submit" class="btn-primary btn-sm">Accept</button>
                            </form>
                            <form method="POST" action="{{ route('matrimony.interests.decline', $interest) }}">
                                @csrf
                                <button type="submit" class="btn-secondary btn-sm">Decline</button>
                            </form>
                        @elseif ($interest->status === 'accepted' && $interest->conversation_id)
                            <x-button :href="route('messages.index', $interest->conversation_id)" size="sm">Message</x-button>
                        @endif
                    </div>
                </div>
            @empty
                <x-empty-state icon="heart" title="No interest requests received yet" class="py-6" />
            @endforelse
        </div>

        <div x-show="tab === 'sent'" x-cloak class="mt-4 space-y-4">
            @forelse ($sent as $interest)
                <div class="card card-body flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <a href="{{ route('matrimony.show', $interest->profile) }}" class="font-semibold text-slate-900 dark:text-white hover:underline">{{ $interest->profile->display_name }}</a>
                        <p class="text-sm text-slate-500 dark:text-slate-400">Sent {{ $interest->created_at->diffForHumans() }}</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <x-badge :variant="match($interest->status) { 'accepted' => 'success', 'declined', 'expired' => 'danger', 'withdrawn' => 'neutral', default => 'warning' }">
                            {{ ucfirst($interest->status) }}
                        </x-badge>
                        @if ($interest->status === 'pending')
                            <form method="POST" action="{{ route('matrimony.interests.withdraw', $interest) }}" onsubmit="return confirm('Withdraw this request?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-sm font-medium text-red-600 hover:underline">Withdraw</button>
                            </form>
                        @elseif ($interest->status === 'accepted' && $interest->conversation_id)
                            <x-button :href="route('messages.index', $interest->conversation_id)" size="sm">Message</x-button>
                        @endif
                    </div>
                </div>
            @empty
                <x-empty-state icon="heart" title="No interest requests sent yet" class="py-6" />
            @endforelse
        </div>
    </div>
</x-layouts::alumni>
