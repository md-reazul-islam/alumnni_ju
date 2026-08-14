@if ($profiles->isEmpty())
    <x-empty-state icon="users" title="No alumni found" description="Try adjusting your search filters." />
@else
    <p class="mb-4 text-sm text-slate-500 dark:text-slate-400">{{ $profiles->total() }} alumni found</p>

    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ($profiles as $profile)
            <div class="card card-body">
                <a href="{{ route('alumni.profile.show', $profile->user) }}" class="flex items-start gap-3">
                    <x-avatar :src="$profile->user->avatar_url" :name="$profile->user->full_name" size="lg" />
                    <div class="min-w-0 flex-1">
                        <p class="flex items-center gap-1 truncate font-semibold text-slate-900 dark:text-white">
                            {{ $profile->user->full_name }}
                            @if ($profile->isVerified())<x-icon name="badge-check" class="h-4 w-4 flex-shrink-0 text-navy-600" />@endif
                        </p>
                        <p class="truncate text-sm text-slate-500 dark:text-slate-400">{{ $profile->degree?->abbreviation }} {{ $profile->graduation_year }}</p>
                        <p class="truncate text-xs text-slate-400">{{ $profile->department?->name }}</p>
                    </div>
                </a>

                @if ($profile->job_title || $profile->organization)
                    <p class="mt-3 truncate text-xs font-medium text-navy-600 dark:text-navy-300">
                        {{ $profile->job_title }} @if($profile->organization) &middot; {{ $profile->organization }} @endif
                    </p>
                @endif

                <div class="mt-4 flex gap-2 border-t border-slate-100 pt-3 dark:border-navy-800">
                    <x-button :href="route('alumni.profile.show', $profile->user)" variant="secondary" size="sm" class="flex-1 justify-center">View Profile</x-button>
                    @auth
                        @if (auth()->id() !== $profile->user_id && Route::has('connections.store'))
                            <button type="button" onclick="AlumniNetwork.sendConnectionRequest({{ $profile->user_id }}, this)" class="btn-ghost btn-sm flex-1 justify-center">Connect</button>
                        @endif
                    @endauth
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-8">{{ $profiles->links() }}</div>
@endif
