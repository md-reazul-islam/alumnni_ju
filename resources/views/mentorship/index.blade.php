<x-layouts::alumni :title="'Mentorship'">
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Mentorship</h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Connect with experienced alumni or offer your own guidance.</p>
        </div>
        <x-button :href="route('mentorship.mine')" variant="secondary" size="sm">My Mentorships</x-button>
    </div>

    <div x-data="{ open: false }" class="card card-body mt-6">
        @if ($myMentorProfile)
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-slate-900 dark:text-white">You're listed as a mentor</p>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Expertise: {{ $myMentorProfile->expertise }}</p>
                </div>
                <form method="POST" action="{{ route('mentorship.toggle-active') }}">
                    @csrf
                    <button type="submit" class="btn-secondary btn-sm">{{ $myMentorProfile->is_active ? 'Pause Listing' : 'Reactivate' }}</button>
                </form>
            </div>
        @else
            <button @click="open = !open" class="flex items-center gap-2 text-sm font-semibold text-navy-700 dark:text-navy-300">
                <x-icon name="handshake" class="h-4 w-4" /> Become a Mentor
            </button>
            <form method="POST" action="{{ route('mentorship.become-mentor') }}" x-show="open" x-cloak class="mt-4 space-y-4 border-t border-slate-100 pt-4 dark:border-navy-800">
                @csrf
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <x-input label="Area of Expertise" name="expertise" required />
                    <x-input label="Industry" name="industry" />
                </div>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <x-input label="Years of Experience" name="experience_years" type="number" min="0" />
                    <x-input label="Availability" name="availability" placeholder="e.g. Weekday evenings" />
                </div>
                <x-input label="Mentorship Topics" name="topics" placeholder="Career growth, technical interviews..." />
                <x-textarea label="Bio" name="bio" rows="3" />
                <div class="flex justify-end"><x-button type="submit" size="sm">Save Mentor Profile</x-button></div>
            </form>
        @endif
    </div>

    <form method="GET" class="mt-6 flex flex-col gap-3 sm:flex-row">
        <input type="text" name="expertise" value="{{ request('expertise') }}" placeholder="Search by expertise" class="form-input sm:max-w-xs">
        <input type="text" name="industry" value="{{ request('industry') }}" placeholder="Search by industry" class="form-input sm:max-w-xs">
        <x-button type="submit" variant="secondary">Search</x-button>
    </form>

    @if ($mentors->isEmpty())
        <x-empty-state icon="handshake" title="No mentors available yet" class="mt-8" />
    @else
        <div class="mt-8 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($mentors as $mentor)
                <div class="card card-body">
                    <div class="flex items-center gap-3">
                        <x-avatar :src="$mentor->user->avatar_url" :name="$mentor->user->full_name" />
                        <div class="min-w-0">
                            <p class="truncate font-semibold text-slate-900 dark:text-white">{{ $mentor->user->full_name }}</p>
                            <p class="truncate text-xs text-slate-500 dark:text-slate-400">{{ $mentor->expertise }}</p>
                        </div>
                    </div>
                    @if ($mentor->bio)
                        <p class="mt-3 line-clamp-2 text-sm text-slate-500 dark:text-slate-400">{{ $mentor->bio }}</p>
                    @endif
                    <div class="mt-3 flex flex-wrap gap-1.5">
                        @if ($mentor->industry)<x-badge variant="neutral">{{ $mentor->industry }}</x-badge>@endif
                        @if ($mentor->experience_years)<x-badge variant="info">{{ $mentor->experience_years }}+ yrs</x-badge>@endif
                    </div>

                    @if ($requestedMentorIds->contains($mentor->user_id))
                        <x-badge variant="success" class="mt-4">Request sent</x-badge>
                    @else
                        <form method="POST" action="{{ route('mentorship.request', $mentor) }}" class="mt-4">
                            @csrf
                            <x-button type="submit" size="sm" class="w-full justify-center">Request Mentorship</x-button>
                        </form>
                    @endif
                </div>
            @endforeach
        </div>
        <div class="mt-8">{{ $mentors->links() }}</div>
    @endif
</x-layouts::alumni>
